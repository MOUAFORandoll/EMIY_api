<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\Notification;
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
use Knp\Component\Pager\PaginatorInterface;

class GeneralController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $paginator;

    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction,

        PaginatorInterface $paginator

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
        $this->paginator = $paginator;
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
                            = ['id' => $produit0->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
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
                                = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                        }
                        if (empty($limgB)) {
                            $limgB[]
                                = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
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










    ///// Ici on gere le cote notification du systeme d'un utilisateur




    /**
     * @Route("/notifications", name="UserNotification", methods={"GET"})
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
     * ici on recupere les notifications de l'utilisateur
     * 
     * 
     */
    public function UserNotification(Request $request)
    {



        if (empty($request->get('keySecret'))) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 400);
        }

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if (!$user) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }

        $list_notifications_final  = [];


        $result = $this->em->getRepository(Notification::class)->findBy(['recepteur' => $user]);
        $page =
            $request->get('page') ?? 1;



        $lNotification = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        foreach ($lNotification as $notification) {


            $notificationU =

                $this->myFunction->modelNotification($notification);

            if ($notificationU != null) {


                $list_notifications_final[] = $notificationU;
            }
        }

        $datas
            = $this->serializer->serialize(array_reverse($list_notifications_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }



    /**
     * @Route("/notifications/read", name="UserReadNotification", methods={"GET"})
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
     * ici l'utilisateur lit la notification
     * 
     * 
     */
    public function UserReadNotification(Request $request)
    {



        if (empty($request->get('id'))) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 400);
        }



        $notification = $this->em->getRepository(Notification::class)->findOneBy(['id' => $request->get('id')]);
        $notification->setRead(true);
        $this->em->persist($notification);
        $this->em->flush();
        return
            new JsonResponse([
                'message'
                =>
                'Success',

            ], 200);
    }


    /**
     * @Route("/home", name="HomeRead", methods={"GET"})
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
    public function HomeRead(Request $request)
    {

        $possible = false;
        // $keySecret = $request->get('keySecret')
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);



        $Produit = $this->getHomeProduct($user);
        $Categorie = $this->getHomeCategory();
        $Boutique = $this->getHomeBoutique($user);


        return
            new JsonResponse([
                'Categorie'
                =>
                $Categorie, 'Produit'
                =>
                $Produit, 'Boutique'
                =>
                $Boutique,

            ], 203);
    }

    /**
     *  Ici  on recupere les produits populaires du home
     * 
     */
    public function getHomeProduct($user)
    {






        $result = $this->em->getRepository(Produit::class)->findAll();
        $lProduit = $this->paginator->paginate($result, 1, $this->myFunction::PAGINATION);
        $data = [];

        foreach ($lProduit  as $produit) {
            $lsImgP = [];
            $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
            foreach ($lProduitO  as $produit0) {
                $lsImgP[]
                    = ['id' => $produit0->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
            }



            $produit =  [
                'id' => $produit->getId(),
                'like' => $this->myFunction->isLike_Produit($produit->getId()),
                'islike' =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),

                'codeProduit' => $produit->getCodeProduit(),
                'boutique' => $produit->getBoutique()->getTitre(),
                'description' => $produit->getDescription(),
                'titre' => $produit->getTitre(),
                'quantite' => $produit->getQuantite(),
                'prix' => $produit->getPrixUnitaire(),
                'status' => $produit->isStatus(),
                'negociable' => $produit->isNegociable(),
                // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
                'images' => $lsImgP

            ];
            array_push($data, $produit);
        }
        // $listProduits = $serializer->serialize($lP, 'json');

        return
            $data;
    }




    /**
     *Ici  on recupere les categorie  du home
     * 
     * 
     */
    public function getHomeCategory()
    {

        $possible = false;




        $result = $this->em->getRepository(Category::class)->findAll();
        $lCategory = $this->paginator->paginate($result, 1, $this->myFunction::PAGINATION);


        $data = [];
        foreach ($lCategory as $category) {


            $categoryU = [
                'id' => $category->getId(),
                'libelle' => $category->getLibelle(),
                'logo' => $this->myFunction::BACK_END_URL . '/images/category/' . $category->getLogo(),
                'description' => $category->getDescription(),
                // 'titre' => $category->getTitre(), 
                'status' => $category->isStatus(),

            ];
            array_push($data, $categoryU);
        }


        return
            $data;
    }


    /**
     * 
     * 
     * Ici on recupere les boutiques populaire du systeme
     * 
     * 
     */
    public function getHomeBoutique($user)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);



        $result    = $this->em->getRepository(Boutique::class)->findAll();
        $lBoutique = $this->paginator->paginate($result, 1, $this->myFunction::PAGINATION);

        $data = [];
        foreach ($lBoutique as $boutique) {
            if ($boutique->getUser()) {

                $lBo   = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                $limgB = [];

                foreach ($lBo as $bo) {
                    $limgB[]
                        = ['id' => $bo->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                }
                if (empty($limgB)) {
                    $limgB[]
                        = ['id' => 0, 'src' => $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                }

                if ($boutique->getUser()) {
                    $boutiqueU = [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),
                        'status_abonnement' => $this->myFunction->userabonnementBoutique($boutique, $user),
                        'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                        'images' => $limgB,
                        'localisation' => $boutique->getLocalisation() ? [
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
        }

        return
            $data;
    }
}
