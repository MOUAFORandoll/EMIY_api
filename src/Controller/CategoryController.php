<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use App\FunctionU\MyFunction;
use FFI\Exception;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\UserPlateform;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{


    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;

    public function __construct(
        SerializerInterface $serializer,
        MyFunction  $myFunction,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb

    ) {
        $this->em = $em;
        $this->myFunction = $myFunction;
        $this->serializer = $serializer;
        $this->clientWeb = $clientWeb;
    }

    public function getUniqueCodeProduit()
    {


        $chaine = 'produit';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $chaine]);
        if ($ExistCode) {
            return
                $this->getUniqueCodeProduit();
        } else {
            return $chaine;
        }
    }


    /**
     * @Route("/category/read", name="categoryRead", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function categoryRead(Request $request)
    {

        $possible = false;




        $lCategory = $this->em->getRepository(Category::class)->findAll();

        if ($lCategory) {

            $lC = [];
            foreach ($lCategory  as $category) {

                if ($category->isStatus()) {
                    $categoryU =  [
                        'id' => $category->getId(),
                        'libelle' => $category->getLibelle(),
                        'logo' => $this->myFunction::BACK_END_URL . '/images/category/' . $category->getLogo(),
                        'description' => $category->getDescription(),
                        // 'titre' => $category->getTitre(), 
                        'status' => $category->isStatus(),

                    ];
                    array_push($lC, $categoryU);
                }
            }
            $lCategoryF =   $this->serializer->serialize($lC, 'json');

            return
                new JsonResponse(
                    [
                        'data'
                        => JSON_DECODE($lCategoryF)
                    ],
                    200
                );
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucun produit'
                ], 203);
        }
    }


    /**
     * @Route("/category/read/boutique", name="categoryReadBoutiqueClient", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     * @param array $data doit contenir la  la keySecret du
     * 
     * 
     */
    public function categoryReadBoutiqueClient(Request $request)
    {



        if (empty($request->get('id'))) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page   '
            ], 400);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $id = $request->get('id');
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $id]);

        $boutiques = $this->em->getRepository(Boutique::class)->findBy(['category' => $category]);

        if ($boutiques) {

            $lB = [];
            foreach ($boutiques   as $boutique) {
                if ($boutique->isStatus()) {
                    $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                    $limgB = [];

                    foreach ($lBo  as $bo) {
                        $limgB[]
                            = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                    }
                    if (empty($limgB)) {
                        $limgB[]
                            = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                    }
                    $boutiqueU =  [
                         'codeBoutique' => $boutique->getCodeBoutique(),
                        'nombre_produit' => count($boutique->getProduits()),
                       
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),
                        'status_abonnement' => $this->myFunction->userabonnementBoutique($boutique, $user),

                        'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                        'images' => $limgB,
                        'localisation' =>  $boutique->getLocalisation() ? [
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
                        // 'localisation' =>  $boutique->getLocalisation() ? [
                        //     'ville' =>
                        //     $boutique->getLocalisation()->getVille(),

                        //     'longitude' =>
                        //     $boutique->getLocalisation()->getLongitude(),
                        //     'latitude' =>
                        //     $boutique->getLocalisation()->getLatitude(),
                        // ] : []
                        // 'produits' => $listProduit,


                    ];
                    array_push($lB, $boutiqueU);
                }
            }
            // $listProduits =   $this->serializer ->serialize($lP, 'json');

            return
                new JsonResponse(
                    [
                        'data'
                        => /*  JSON_DECODE(
                            $listProduits
                        ) */ $lB,
                        'statusCode' => 200
                    ],
                    200
                );
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Une Erreur est survenue'
                ], 203);
        }
    }
}
