<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\UserPlateform;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

use FFI\Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ActionController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction

    ) {
         $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/search", name="search", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $data = $request->toArray();
        // if (empty($data['type']) || empty($data['search'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayer '
        //     ], 400);
        // }
        $a = 0;
        $type = $data['type'];
        $search = $data['search'];
        $finalData = [];
        if ((int)  $type == 0) {
            $finalData =    $this->searchProduit($search);
        }
        if ((int)  $type == 1) {
            $a
                = 100;

            $finalData =    $this->searchBoutique($search);
        }
        if ((int)  $type == 2) {
            $finalData =   $this->searchCategory($search);
        }
        return new JsonResponse([
            'data' => $finalData,


        ], 200);
    }



    public function searchProduit($search)
    {
        $data = [];


        $lProduit = $this->em->getRepository(Produit::class)->findAll();
        if ($lProduit) {
            foreach ($lProduit  as $produit) {
                if (str_contains(

                    strtolower(
                        $produit->getTitre()
                    ),
                    strtolower($search)

                )) {



                    $lsImgP = [];
                    $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                    foreach ($lProduitO  as $produit0) {
                        $lsImgP[]
                            = ['id' => $produit0->getId(), 'src' =>   /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/produits/' . $produit0->getSrc()];
                    }



                    $produit =  [
                        'id' => $produit->getId(),
                        'codeProduit' => $produit->getCodeProduit(),
                        'boutique' => $produit->getBoutique()->getTitre(),
                        'description' => $produit->getDescription(),
                        'titre' => $produit->getTitre(),
                        'quantite' => $produit->getQuantite(),
                        'prix' => $produit->getPrixUnitaire(),
                        'status' => $produit->isStatus(),
                        // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
                        'images' => $lsImgP

                    ];
                    array_push($data, $produit);
                }
                // $listProduits = $serializer->serialize($lP, 'json');
            }
            return
                $data;
        }
    }

    public function searchBoutique($search)
    {
        $data = [];


        $lBoutique = $this->em->getRepository(Boutique::class)->findAll();

        if ($lBoutique) {


            foreach ($lBoutique  as $boutique) {



                if (
                    str_contains(
                        strtolower($boutique->getTitre()),
                        strtolower($search)
                    )
                ) {

                    if ($boutique->getUser()) {

                        $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                        $limgB = [];

                        foreach ($lBo  as $bo) {
                            $limgB[]
                                = ['id' => $bo->getId(), 'src' =>   /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/boutiques/' . $bo->getSrc()];
                        }
                        if (empty($limgB)) {
                            $limgB[]
                                = ['id' => 0, 'src' =>   /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/default/boutique.png'];
                        }
                        $boutiqueU =  [
                            'codeBoutique' => $boutique->getCodeBoutique(),
                            'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                            'description' => $boutique->getDescription() ?? "Aucune",
                            'titre' => $boutique->getTitre() ?? "Aucun",
                            'status' => $boutique->isStatus(),

                            'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                            'images' => $limgB,
                            'localisation' =>  $boutique->getLocalisation() != null ? [
                                'ville' =>
                                $boutique->getLocalisation()->getVille(),

                                'longitude' =>
                                $boutique->getLocalisation()->getLongitude(),
                                'latitude' =>
                                $boutique->getLocalisation()->getLatitude(),
                            ] : [
                                'ville' =>
                                'incertiane',

                                'longitude' =>
                                0.0,
                                'latitude' =>
                                0.0,
                            ]
                            // 'produits' => $listProduit,


                        ];
                        array_push($data, $boutiqueU);
                    }
                }
                // $listBoutiques = $serializer->serialize($lB, 'json');

                return $data;
            }
        }
        return
            $data;
    }

    public function searchCategory($search)
    {
        $data = [];
        $lCategory = $this->em->getRepository(Category::class)->findAll();

        if ($lCategory) {



            foreach ($lCategory  as $category) {
                if (
                    str_contains(
                        strtolower($category->getLibelle()),
                        strtolower($search)

                    )
                ) {
                    if ($category->isStatus()) {
                        $categoryU =  [
                            'id' => $category->getId(),
                            'libelle' => $category->getLibelle(),
                            'icon' => $category->getFlutterIcon(),
                            'description' => $category->getDescription(),
                            // 'titre' => $category->getTitre(), 
                            'status' => $category->isStatus(),

                        ];
                        array_push($data, $categoryU);
                    }
                }
            }
        }
        return
            $data;
    }



 
}
