<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commission;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use FFI\Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\UserPlateform;
use App\FunctionU\MyFunction;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


class LinkController extends AbstractController
{


    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;
    private $paginator;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,

        MyFunction  $myFunction,

        PaginatorInterface $paginator
    ) {
        $this->paginator = $paginator;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }


    /**
     * @Route("/link/produit/read", name="produitReadUniLink", methods={"GET"})
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
    public function produitReadUniLink(Request $request,)
    {
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $request->get('codeProduit')]);
        if (!$produit) {
            return
                new JsonResponse(
                    [
                        'message'
                        => 'Introuvable',



                    ],
                    203
                );
        }
        # code... 

        if (!$produit->isStatus() && $produit->getQuantite() < 0) {
            return
                new JsonResponse(
                    [
                        'message'
                        => 'Indisponible',



                    ],
                    203
                );
        }
        $produitF =
            $this->myFunction->ProduitModel($produit, $user);





        return
            new JsonResponse(
                [
                    'data'
                    =>    $produitF,



                ],
                200
            );
    }




    /**
     * @Route("/link/boutique/read", name="BoutiqueReadUniLink", methods={"GET"})
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
     * 
     */
    public function BoutiqueReadUniLink(Request $request,)
    {
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $request->get('codeBoutique')]);
        if (!$boutique) {
            return
                new JsonResponse(
                    [
                        'message'
                        => 'Introuvable',



                    ],
                    203
                );
        }
        # code... 

        if (!$boutique->getUser()) {

            return
                new JsonResponse(
                    [
                        'message'
                        => 'Introuvable',



                    ],
                    203
                );
        }

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
        $listProduit = [];
        foreach ($boutique->getProduits()  as $produit) {
            if ($produit->isStatus()) {
                $produitF =
                    $this->myFunction->ProduitModel($produit, $user);

                array_push($listProduit, $produitF);
            }
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
            ], 'produits' => $listProduit


        ];


        return
            new JsonResponse(
                [
                    'data'
                    =>    $boutiqueU,



                ],
                200
            );
    }
}
