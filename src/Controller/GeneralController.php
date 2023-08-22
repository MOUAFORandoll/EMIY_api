<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\Notification;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Short;
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
     * @Route("/search", name="search", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        // if (empty($data['type']) || empty($data['search'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayer '
        //     ], 400);
        // }

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        /**   si type == 0 , on update la recharche de produit uniquement , 1 =>boutique, 2 =>categorie   , 3 =>shorts      tous ceci en faisant la pagination et sans toute fois toucher aux autres resultats*/
        $type = $request->get('type');
        $page = $request->get('page') ?? 1;
        if ($type != null && $page != null) {
            if ($type == 0) {

                $produit =    $this->searchProduit($search, $user, $page);



                return new JsonResponse([
                    'produit' => $produit, 'type' => 0


                ], 200);
            }
            if ($type == 1) {



                $boutique =    $this->searchBoutique($search, $user, $page);


                return new JsonResponse([

                    'boutique' => $boutique, 'type' => 1


                ], 200);
            }
            if ($type == 2) {


                $categorie =   $this->searchCategory($search, $user, $page);

                return new JsonResponse([

                    'categorie' => $categorie, 'type' => 2


                ], 200);
            }
            if ($type == 3) {


                $short =   $this->searchShort($search, $user, $page);

                return new JsonResponse([

                    'short' => $short,
                    'type' => 3


                ], 200);
            }
        }

        $finalData = [];

        $produit =    $this->searchProduit($search, $user, $page);


        $boutique =    $this->searchBoutique($search, $user, $page);

        $categorie =   $this->searchCategory($search, $user, $page);
        $short     = $this->searchShort($search, $user, $page);
        return new JsonResponse([
            'produit' => $produit,
            'boutique' => $boutique,
            'categorie' => $categorie,
            'short' => $short,


        ], 200);
    }



    public function searchProduit($search,  $user, $page)
    {
        $data = [];


        $result = $this->em->getRepository(Produit::class)->findByTitre($search);
        $lProduit = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        foreach ($lProduit as $produit) {





            $produitF =
                $this->myFunction->ProduitModel($produit, $user);

            array_push($data, $produitF);

            // $listProduits = $serializer->serialize($lP, 'json');
        }
        return
            $data;
    }

    public function
    searchBoutique($search,  $user, $page)
    {
        $data = [];


        $result = $this->em->getRepository(Boutique::class)->findByTitre($search);
        $lBoutique = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);

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

            // $listBoutiques = $serializer->serialize($lB, 'json');


        }
        return
            $data;
    }

    public function
    searchCategory($search, $user, $page)
    {
        $data = [];
        $result = $this->em->getRepository(Category::class)->findByTitre($search);
        $lCategory = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);

        foreach ($lCategory as $category) {





            if ($category->isStatus()) {
                $categoryU =  [
                    'id' => $category->getId(),
                    'libelle' => $category->getLibelle(),
                    'logo' => $this->myFunction::BACK_END_URL . '/images/category/' . $category->getLogo(),

                    'description' => $category->getDescription(),
                    // 'titre' => $category->getTitre(), 
                    'status' => $category->isStatus(),

                ];
                array_push($data, $categoryU);
            }
        }
        return
            $data;
    }


    public function
    searchShort($search,  $user, $page)
    {
        $data = [];
        $result = $this->em->getRepository(Short::class)->findByTitre($search);
        $lShort = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);

        foreach ($lShort as $short) {



            $boutique = $short->getBoutique();

            if ($boutique) {
                if ($boutique->isStatus()) {
                    $shortF =     $this->myFunction->ShortModel($short, $user);
                    //     $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                    //     $limgB = [];

                    //     foreach ($lBo  as $bo) {
                    //         $limgB[]
                    //             = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                    //     }
                    //     if (empty($limgB)) {
                    //         $limgB[]
                    //             = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                    //     }
                    //     $boutiqueU =  [
                    //         'codeBoutique' => $boutique->getCodeBoutique(),
                    //         'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                    //         'description' => $boutique->getDescription() ?? "Aucune",
                    //         'titre' => $boutique->getTitre() ?? "Aucun",
                    //         'status' => $boutique->isStatus(),
                    //         'note' => $this->myFunction->noteBoutique($boutique->getId()),

                    //         'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                    //         'images' => $limgB,
                    //         'localisation' =>  $boutique->getLocalisation() ? [
                    //             'ville' =>
                    //             $boutique->getLocalisation()->getVille(),

                    //             'longitude' =>
                    //             $boutique->getLocalisation()->getLongitude(),
                    //             'latitude' =>
                    //             $boutique->getLocalisation()->getLatitude(),
                    //         ] : [
                    //             'ville' =>
                    //             'incertiane',

                    //             'longitude' =>
                    //             0.0,
                    //             'latitude' =>
                    //             0.0,
                    //         ]
                    //     ];


                    //     $shortF =  [

                    //         'id' => $short->getId(),
                    //         'titre' => $short->getTitre() ?? "Aucun",
                    //         'description' => $short->getDescription() ?? "Aucun",
                    //         'status' => $short->isStatus(),
                    //         'Preview' =>  $short->getPreview(),
                    //         'is_like' =>   $user == null ? false : $this->myFunction->userlikeShort($short, $user),
                    //         'src' =>  $short->getSrc(),
                    //         'codeShort' =>
                    //         $short->getCodeShort(), 'nbre_like' => count($this->myFunction->ListLikeShort($short)),
                    //         'nbre_commentaire' => count($this->myFunction->ListCommentShort($short)),
                    //         'date' =>
                    //         date_format($short->getDateCreated(), 'Y-m-d H:i'),
                    //         'boutique' =>  $boutiqueU

                    //     ];
                    array_push($data, $shortF);
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
        $lProduit = $this->paginator->paginate($result, 1,  $this->myFunction::PAGINATION);
        $listProduit = [];

        foreach ($lProduit  as $produit) {
            $produitF =
                $this->myFunction->ProduitModel($produit, $user);

            array_push($listProduit, $produitF);
        }
        // $listProduits = $serializer->serialize($lP, 'json');

        return
            $listProduit;
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
