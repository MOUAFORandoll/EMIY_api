<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Commande;
use App\Entity\Compte;
use App\Entity\ListCommandeLivreur;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\Panier;
use App\Entity\Place;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use App\Entity\TypeCommande;
use App\Entity\TypeUser;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime;
use FFI\Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\UserPlateform;
use App\FunctionU\MyFunction;
use PHPUnit\TextUI\Command;
use Symfony\Component\Console\Command\ListCommand;

use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class DashBoardController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $client;
    private $validator;
    private $myFunction;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,

        HttpClientInterface $client,

        ValidatorInterface $validator,
        MyFunction $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;

        $this->client = $client;


        $this->validator = $validator;
        $this->myFunction = $myFunction;
    }



    /**
     * @Route("/dashboard/commande", name="DashBoardcommandeReadAll", methods={"GET"})
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
    public function DashBoardcommandeReadAll(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $possible = false;
        if (empty($request->get('keySecret'))) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et  preciser votre keySecret '
            ], 400);
        }
        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if ($admin) {



            if (
                $admin->getTypeUser()->getId() == 1
            ) {

                $llcom
                    = $this->em->getRepository(Commande::class)->findAll();

                $fCom = [];
                foreach ($llcom  as $commande) {


                    if ($commande) {


                        $llcom
                            = $this->em->getRepository(ListCommandeLivreur::class)->findBy(['commande' => $commande]);
                        if (!$llcom) {
                            $panier = $commande->getPanier();

                            if ($panier) {

                                $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);
                                $lP = [];
                                if ($listProduitPanier) {

                                    foreach ($listProduitPanier  as $pp) {

                                        $produit = $pp->getProduit();

                                        if ($produit) {




                                            if (
                                                $produit->getBoutique()
                                            ) {

                                                $lsImgP = [];
                                                $lProduitO =   $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                                foreach ($lProduitO  as $produit0) {
                                                    $lsImgP[]
                                                        = ['id' => $produit0->getId(), 'src' =>  /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/produits/' . $produit0->getSrc()];
                                                }
                                                $boutique = [

                                                    'id'  => $produit->getBoutique()->getId(),

                                                    'titre'
                                                    => $produit->getBoutique()->getTitre(),
                                                    'codeBoutique'
                                                    => $produit->getBoutique()->getCodeBoutique(),
                                                    'description'
                                                    => $produit->getBoutique()->getDescription(),

                                                    'localisation' =>  $produit->getBoutique()->getLocalisation() ? [
                                                        'ville' =>
                                                        $produit->getBoutique()->getLocalisation()->getVille(),

                                                        'longitude' =>
                                                        $produit->getBoutique()->getLocalisation()->getLongitude(),
                                                        'latitude' =>
                                                        $produit->getBoutique()->getLocalisation()->getLatitude(),
                                                    ] : []
                                                ];
                                                $produit =  [
                                                    'idBoutique'  => $produit->getBoutique()->getId(),
                                                    'codeProduit' => $pp->getCodeProduitPanier(),
                                                    // 'codeCommande' => $commande->getCodeCommande(),
                                                    'boutique' => $boutique,
                                                    'titre' => $produit->getTitre(),
                                                    'prix' => $produit->getPrixUnitaire(),
                                                    'quantite' => $pp->getQuantite(),
                                                    'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                                    'photo' => $lsImgP[0]
                                                ];
                                                array_push($lP, $produit);
                                            }
                                        }
                                    }
                                }
                            }
                            $lff = [];
                            foreach ($lP  as $p) {

                                foreach ($lP  as $y) {
                                    if ($p['idBoutique'] == $y['idBoutique']) {
                                        if (!in_array($y, $lff))
                                            array_push($lff, $y);
                                    }
                                }
                            }


                            if (count($lP) > 0) {
                                $cf = [
                                    'status' => $this->getStatustoText($commande->getStatusFinish()),
                                    'user_name' =>   $commande->getPanier()->getNomClient() . ' ' .  $commande->getPanier()->getPrenomClient(),
                                    'user_phone' =>   $commande->getPanier()->getPhoneClient(),
                                    'codeCommande' =>  $commande->getCodeCommande(),
                                    'montant' => $commande->getMontant(),
                                    'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                                    'nombre_produit' =>   count($listProduitPanier),

                                    'produits' => $lff,
                                    'point_livraison' => $commande->getPointLivraison()->getLibelle()
                                ];
                                array_push($fCom, $cf);
                            }
                            // $listProduits = $serializer->serialize($lP, 'json');



                        }
                    }
                }
                return
                    new JsonResponse(
                        [

                            'data'
                            =>  $fCom
                        ],
                        200
                    );
            } else {
                return new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Action impossible'
                ], 203);
            }
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Utilisateur introuvable'
            ], 400);
        }
    }
    /**
     * @Route("/dashboard/boutique", name="DashBoardboutiqueReadAll", methods={"GET"})
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
    public function DashBoardboutiqueReadAll(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);



        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if (!$admin) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }

        if (
            $admin->getTypeUser()->getId() != 1
        ) {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }
        $lBoutique = $this->em->getRepository(Boutique::class)->findAll();
        if (!$lBoutique) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Aucune Boutique'
            ], 203);
        }


        $lB = [];
        foreach ($lBoutique  as $boutique) {
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

                if ($boutique->getUser()) {
                    $boutiqueU =  [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),

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
                        // 'produits' => $listProduit,


                    ];
                    array_push($lB, $boutiqueU);
                }
            }
        }

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lB,
                    'statusCode' => 200

                ],
                200
            );
    }

    /**
     * @Route("/dashboard/boutique/produit", name="DashBoardboutiqueReadProduit", methods={"GET"})
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
    public function DashBoardboutiqueReadProduit(Request $request)
    {



        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;

        if (

            empty($request->get('codeBoutique'))
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }
        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if (!$admin) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }

        if (
            $admin->getTypeUser()->getId() != 1
        ) {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }

        $codeBoutique = $request->get('codeBoutique');



        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);

        if ($boutique) {
            $listProduit = [];
            foreach ($boutique->getProduits()  as $produit) {
                if ($produit->isStatus()) {
                    $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                    $lsImgP = [];

                    foreach ($lProduitO  as $produit0) {
                        $lsImgP[]
                            = ['id' => $produit0->getId(), 'src' =>   /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/produits/' . $produit0->getSrc()];
                    }
                    $listProduit[] = [
                        'id' => $produit->getId(),
                        'codeProduit' => $produit->getCodeProduit(),
                        'titre' => $produit->getTitre(),
                        'quantite' => $produit->getQuantite(),
                        'prix' => $produit->getPrixUnitaire(),
                        'description' => $produit->getDescription(),
                        'status' => $produit->isStatus(),
                        'like' => $this->myFunction->isLike_Produit($produit->getId()),

                        'date' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
                        'negociable' => $produit->isNegociable(),
                        'images' => $lsImgP
                    ];
                }
            }


            return
                new JsonResponse(
                    [

                        'data'
                        =>    $listProduit

                    ],
                    200
                );
        } else {
            return
                new JsonResponse([
                    'exist' => false,

                    'data'
                    => [],
                    'message' => 'Aucune Boutique'
                ], 200);
        }
    }

    /**
     * @Route("/dashboard/boutique/commande", name="DashBoardboutiqueReadCommande", methods={"GET"})
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
    public function DashBoardboutiqueReadCommande(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);


        if (
            /*     empty($data['keySecret']) || */
            empty($request->get('codeBoutique'))
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }


        $codeBoutique = $request->get('codeBoutique');
        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if (!$admin) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }

        if (
            $admin->getTypeUser()->getId() != 1
        ) {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }
        $lcom = $this->em->getRepository(Commande::class)->findAll();
        $lP = [];
        foreach ($lcom  as $commande) {
            if ($commande) {
                $panier = $commande->getPanier();

                if ($panier) {

                    $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);

                    if ($listProduitPanier) {
                        foreach ($listProduitPanier  as $pp) {

                            $produit = $pp->getProduit();

                            if ($produit) {

                                if ($produit->getBoutique()) {

                                    if (
                                        $produit->getBoutique()->getCodeBoutique() == $codeBoutique
                                    ) {


                                        $com =   [
                                            'status' => $this->getStatustoText($commande->getStatusFinish()),
                                            'user_name' =>   $commande->getPanier()->getNomClient() . ' ' .  $commande->getPanier()->getPrenomClient(),
                                            'user_phone' =>   $commande->getPanier()->getPhoneClient(),
                                            'codeCommande' =>  $commande->getCodeCommande(),
                                            'montant' => $commande->getMontant(),
                                            'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                                            'nombre_produit' =>   count($listProduitPanier),

                                            'point_livraison' => $commande->getPointLivraison()->getLibelle()
                                        ];
                                        array_push($lP, $com);
                                    }
                                }
                            }
                        }
                    }
                    // $listProduits = $serializer->serialize($lP, 'json');


                }
            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $lP,
                    'codeBoutique' => $codeBoutique
                ],
                200
            );
    }











    /**
     * @Route("/dashboard/user", name="DashBoardUser", methods={"GET"})
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
     * @param array $data doit contenir la cle secrete du user
     * 
     * 
     */
    public function DashBoardUser(Request $request)
    {


        if (empty($request->get('keySecret'))) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 400);
        }
        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if (!$admin) {


            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }
        if (
            $admin->getTypeUser()->getId() != 1
        ) {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }
        $list_users_final = [];


        $luser = $this->em->getRepository(UserPlateform::class)->findAll();

        foreach ($luser as $user) {

            $localisation = $user->getLocalisations()[count($user->getLocalisations()) - 1];
            $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);
            if ($compte) {
                if ($user->getTypeUser()) {
                    $userU = [
                        'id' => $user->getId(),
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'email' => $user->getEmail(),
                        'phone' => $user->getPhone(),
                        'status' => $user->isStatus() ? "Actif" : "inactif",
                        'keySecret' => $user->getKeySecret(),
                        'type_user_id' => $user->getTypeUser()->getId(),
                        'type_user' => $this->getTypeUsertoText($user->getTypeUser()->getId()),
                        'date' => date_format($user->getDateCreated(), 'Y-m-d H:i'),
                        'solde' => $compte->getSolde() ?? 0,
                        'localisation' => $localisation ? [
                            'ville' =>
                            $localisation->getVille(),

                            'longitude' =>
                            $localisation->getLongitude(),
                            'latitude' =>
                            $localisation->getLatitude(),
                        ] : [
                            'ville' =>
                            'Aucune',

                            'longitude' =>
                            0,
                            'latitude' =>
                            0,
                        ]
                        // 'nom' => $user->getNom()
                    ];

                    $list_users_final[] = $userU;
                }
            } else {
                $newCompte = new Compte();

                $newCompte->setUser($user);
                $newCompte->setSolde(0);


                $this->em->persist($newCompte);
                $this->em->flush();    # code...
            }
        }
        $datas
            = $this->serializer->serialize(array_reverse($list_users_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }




    /**
     * @Route("/dashboard/boutique/state", name="DashBoardboutiqueState", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardboutiqueState(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret']) || empty($data['codeBoutique'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   '
            ], 400);
        }

        $keySecret = $data['keySecret'];
        $codeBoutique = $data['codeBoutique'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);

        if ($user->getTypeUser()->getId() != 1) {
            return new JsonResponse([
                'message' => 'Action impossible'

            ], 400);
        }

        $boutique->setStatus(!$boutique->isStatus());




        $this->em->persist($boutique);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Boutique modifiee avec success',

            'id' =>  $boutique->getId()

        ], 200);
    }
    public function getTypeUsertoText($type)
    {


        switch ($type) {


            case 1:
                return
                    'Administrateur';

            case 2:
                return
                    'Client';

            case 3:
                return
                    'Livreur';


            default:
                return
                    'Une erreur systeme';
        }
        # code...
    }

    public function getStatustoText($status)
    {


        switch ($status) {
            case 0:
                return
                    'En attente';

            case 1:
                return
                    'attente du livreur par les boutiques';

            case 2:
                return
                    'En attente du livreur par le client ';

            case 3:
                return
                    'Commande livree';


            default:
                return
                    'Une erreur systeme';
        }
        # code...
    }
}
