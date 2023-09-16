<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Communication;
use App\Entity\Compte;
use App\Entity\ListCommandeLivreur;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
use App\Entity\MessageCommunication;
use App\Entity\MessageNegociation;
use App\Entity\ModePaiement;
use App\Entity\NegociationProduit;
use App\Entity\Notification;
use App\Entity\Panier;
use App\Entity\Place;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use App\Entity\TypeCommande;
use App\Entity\TypeUser;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
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

use Symfony\Component\String\Slugger\SluggerInterface;

class DashBoardController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $client;
    private $validator;
    private $jwt;
    private $jwtRefresh;
    private $myFunction;

    private $passwordHasher;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,

        //security
        UserPasswordHasherInterface      $passwordHasher,

        HttpClientInterface $client,
        JWTTokenManagerInterface $jwt,
        RefreshTokenManagerInterface $jwtRefresh,
        ValidatorInterface $validator,
        MyFunction $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->passwordHasher = $passwordHasher;

        $this->client = $client;

        $this->jwt = $jwt;
        $this->jwtRefresh = $jwtRefresh;

        $this->validator = $validator;
        $this->myFunction = $myFunction;
    }

    /**
     * @Route("/dashboard/auth/user", name="DashBoardauthU", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardauthU(Request $request)
    {
        $data = $request->toArray();


        if (empty($data['phone'])  || empty($data['password'])) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre numero de telephone et mot de passe.'
            ], 400);
        }

        $phone = $data['phone'];

        $password = $data['password'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['phone' => $phone,]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Ce client n\'existe pas'
            ], 400);
        }

        if (
            $user->getTypeUser()->getId() != 1
        ) {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 203);
        }
        if (!password_verify($password, $user->getPassword())) {
            return new JsonResponse([
                'message' => 'Mauvais mot de passe'
            ], 400);
        }
        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([


            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }

    /**
     * @Route("/dashboard", name="DashBoard", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoard(Request $request)
    {


        if (empty($request->get('keySecret'))) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et  preciser votre keySecret '
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

        $user = $this->em->getRepository(UserPlateform::class)->findAll();
        $llcom
            = $this->em->getRepository(Commande::class)->findAll();

        $listLivraison = $this->em->getRepository(ListCommandeLivreur::class)->findAll();
        $listBoutique = $this->em->getRepository(Boutique::class)->findBy(['status' => false]);

        $data = [

            'nbr_users' => count($user),
            'nbr_commandes'
            => count($llcom),
            'nbr_livraisons'
            => count($listLivraison),

            'nbr_boutiques' => count($listBoutique),
        ];
        return new JsonResponse([

            'data' => $data

        ], 201);
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
                                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
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
                        = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                }
                if (empty($limgB)) {
                    $limgB[]
                        = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                }

                if ($boutique->getUser()) {
                    // $boutique->setDateFirstActivated(new \DateTimeImmutable());

                    // $boutique->setDateLastDesactivated(new \DateTimeImmutable());

                    // $this->em->persist($boutique);
                    // $this->em->flush();
                    $boutiqueU =  [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'nombre_produit' => count($boutique->getProduits()),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),
                        'dateFirstActivated' => date_format($boutique->getDateFirstActivated(), 'Y-m-d H:i'),
                        'dateLastDesactivated' => date_format($boutique->getDateLastDesactivated(), 'Y-m-d H:i'),
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
     * @Route("/dashboard/boutique/request", name="DashBoardboutiqueRequest", methods={"GET"})
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
    public function DashBoardboutiqueRequest(Request $request)
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
        $lBoutique = $this->em->getRepository(Boutique::class)->findBy(['status' => false]);
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
                        = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                }
                if (empty($limgB)) {
                    $limgB[]
                        = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                }

                if ($boutique->getUser()) {
                    $boutiqueU =  [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'nombre_produit' => count($boutique->getProduits()),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),
                        'dateLastDesactivated' => date_format($boutique->getDateLastDesactivated(), 'Y-m-d H:i'),

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

        if (!$boutique->isStatus() && $boutique->getDateFirstActivated() == null) {

            $boutique->setDateFirstActivated(new \DateTimeImmutable());
        } else {
            $boutique->setDateLastDesactivated(new \DateTimeImmutable());
        }
        $boutique->setStatus(!$boutique->isStatus());


        $this->em->persist($boutique);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Boutique modifiee avec success',

            'id' =>  $boutique->getId()

        ], 200);
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
                            = ['id' => $produit0->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
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
                        'status_bool' => $user->isStatus(),
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
     * @Route("/dashboard/client/state", name="DashBoardclientState", methods={"POST"})
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
    public function DashBoardclientState(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();



        if (
            empty($data['keySecret']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin->getTypeUser()->getId() == 1) {

            if ($user) {


                $user->setStatus(!$user->isStatus());

                $this->em->persist($user);
                $this->em->flush();
                return
                    new JsonResponse(
                        [
                            'message'
                            =>  'success'
                        ],
                        200
                    );
            } else {
                return
                    new JsonResponse([
                        'data'
                        => [],
                        'message' => 'Utilisateur introuvable'
                    ], 203);
            }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }



    /**
     * @Route("/dashboard/admin/make", name="DashBoardadminMake", methods={"POST"})
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
    public function DashBoardadminMake(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;



        if (
            empty($data['keySecret']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin->getTypeUser()->getId() == 1) {

            if ($user) {

                $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);
                $user->setTypeUser($typeUser);

                $this->em->persist($user);
                $this->em->flush();
                return
                    new JsonResponse(
                        [
                            'message'
                            =>  'success'
                        ],
                        200
                    );
            } else {
                return
                    new JsonResponse([
                        'data'
                        => [],
                        'message' => 'Utilisateur introuvable'
                    ], 203);
            }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }



    /**
     * @Route("/dashboard/password/reset", name="PasswordReset", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function PasswordReset(Request $request)
    {
        $data = $request->toArray();

        if (
            empty($data['keySecret']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin->getTypeUser()->getId() == 1) {

            if ($user) {



                $npass =   $this->getNewPssw();

                $password = $this->passwordHasher->hashPassword(
                    $user,
                    $npass
                );
                $user->setPassword($password);
                $this->em->persist($user);
                $this->em->flush();
                return
                    new JsonResponse(
                        [
                            'message'
                            =>  'success',
                            'password'
                            => $npass,
                        ],
                        200
                    );
            } else {
                return
                    new JsonResponse([
                        'data'
                        => [],
                        'message' => 'Utilisateur introuvable'
                    ], 203);
            }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }


    /**
     * @Route("/dashboard/livreur/make", name="DashBoardlivreurMake", methods={"POST"})
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
    public function DashBoardlivreurMake(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();



        if (
            empty($data['keySecret']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin) {
            if ($admin->getTypeUser()->getId() == 1) {

                if ($user) {



                    $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);
                    $user->setTypeUser($typeUser);
                    $this->em->persist($user);
                    $this->em->flush();


                    return
                        new JsonResponse(
                            [
                                'message'
                                => 'success'
                            ],
                            200
                        );
                } else {
                    return
                        new JsonResponse([
                            'data'
                            => [],
                            'message' => 'Utilisateur introuvable'
                        ], 203);
                }
            } else {
                return
                    new JsonResponse([
                        'data'
                        => [],
                        'message' => 'Aucune authorisation'
                    ], 203);
            }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }

    /**
     * @Route("/dashboard/client/make", name="DashBoardclientMake", methods={"POST"})
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
    public function DashBoardclientMake(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();



        if (
            empty($data['keySecret']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin) {
            if ($admin->getTypeUser()->getId() == 1) {

                if ($user) {



                    $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 2]);
                    $user->setTypeUser($typeUser);
                    $this->em->persist($user);
                    $this->em->flush();


                    return
                        new JsonResponse(
                            [
                                'message'
                                => 'success'
                            ],
                            200
                        );
                } else {
                    return
                        new JsonResponse([
                            'data'
                            => [],
                            'message' => 'Utilisateur introuvable'
                        ], 203);
                }
            } else {
                return
                    new JsonResponse([
                        'data'
                        => [],
                        'message' => 'Aucune authorisation'
                    ], 203);
            }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }



    /**
     * @Route("/dashboard/notifications", name="DashBoardNotification", methods={"GET"})
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
     * @param array $data doit contenir la cle secrete de l'admin
     * 
     * 
     */
    public function DashBoardNotification(Request $request)
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
        $list_notifications_final  = [];


        $lNotification = $this->em->getRepository(Notification::class)->findAll();

        foreach ($lNotification as $notification) {




            $notificationU = [
                'id' => $notification->getId(),
                'user_create' => $notification->getInitiateur()->getNom() . " " . $notification->getInitiateur()->getNom(),
                'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),

                'title' => $notification->getTitle(),
                'description' => $notification->getDescription(),

            ];

            $list_notifications_final[] = $notificationU;
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
     * @Route("/dashboard/notifications", name="DashBoardNotificationAdd", methods={"POST"})
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
     * @param array $data doit contenir la cle secrete de l'admin
     * 
     * 
     */
    public function DashBoardNotificationAdd(Request $request)
    {

        $data = $request->toArray();



        if (
            empty($data['title'])
            ||  empty($data['description']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $title = $data['title'];
        $description = $data['description'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
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



        $data = [

            "title" => $title,
            "message" => $description
        ];


        $this->myFunction->Socekt_Emit_general($data);


        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setDescription($description);
        $notification->setInitiateur($admin);
        $this->em->persist($notification);
        $this->em->flush();
        return
            new JsonResponse([
                'message'
                =>
                'success',

            ], 200);
    }



    /**
     * @Route("/dashboard/communication/list", name="DashBoardCommunicationList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardCommunicationList(Request $request)
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
        $communication = $this->em->getRepository(Communication::class)->findAll();
        $listCommunication = [];
        foreach ($communication as   $value) {

            $listCommunication[] = [
                'user_create' => $value->getClient()->getNom() . " " . $value->getClient()->getNom(),
                'date' => date_format($value->getDateCreated(), 'Y-m-d H:i'),

                'codeCommunication' => $value->getCodeCommunication(),
            ];
        }
        return new JsonResponse([
            'data' => $listCommunication,




        ], 200);
    }


    /**
     * @Route("/dashboard/communication/message/list", name="DashBoardCommunicationMessageList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardCommunicationMessageList(Request $request)
    {
        $code = $request->query->get('code');

        $communication = $this->em->getRepository(Communication::class)->findOneBy(['codeCommunication' => $code]);
        $messagecommunication = $this->em->getRepository(MessageCommunication::class)->findBy(['communication' => $communication]);
        $messages = [];
        foreach ($messagecommunication as   $value) {

            $messages[] = [
                'message' => $value->getMessage(),
                'is_service' =>       $value->getInitiateur()->getId()
                    == $communication->getClient()->getId() ? 0 : 1,
                'user_create' => $value->getInitiateur()->getNom() . " " . $value->getInitiateur()->getNom(),

                'date' =>  $value->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $value->getDateEnvoi()->format('H:i'),
            ];
        }
        return new JsonResponse([
            'data' => $messages,




        ], 200);
    }




    /**
     * @Route("/dashboard/negociation/list", name="DashBoardnegociationList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardnegociationList(Request $request)
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


        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findAll();
        $negociations = [];
        foreach ($negociationProduit as   $value) {
            $messageNegociationProduit = $this->em->getRepository(MessageNegociation::class)->findBy(['negociation' => $value]);
            $lastElement = array_pop($messageNegociationProduit);

            // Vérifier si $lastElement n'est pas nul avant de l'utiliser
            if ($lastElement !== null) {
                $negociations[] = [
                    'codeNegociation' => $value->getCodeNegociation(),
                    'prixNegocie' => $value->getPrixNegocie(),
                    'titre_produit' =>  $value->getProduit()->getTitre(),
                    'src_produit' =>  $this->myFunction::BACK_END_URL . '/images/produits/' .  $value->getProduit()->getProduitObjects()[0]->getSrc(),
                    'last_message' => ($lastElement)->getMessage(),
                    'date' =>  $value->getDateCreated()->format('Y-m-d'),
                    'heure' =>  $value->getDateCreated()->format('H:i'),
                ];
            }
        }
        return new JsonResponse([
            'data' => $negociations,



        ], 200);
    }
    /**
     * @Route("/dashboard/negociation/message/list", name="DashBoardnegociationMessageList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function DashBoardnegociationMessageList(Request $request)
    {
        $code = $request->query->get('code');
        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findOneBy(['codeNegociation' => $code]);
        $messageNegociationProduit = $this->em->getRepository(MessageNegociation::class)->findBy(['negociation' => $negociationProduit]);
        $messages = [];
        foreach ($messageNegociationProduit as   $value) {
            if ($negociationProduit->getInitiateur() && $negociationProduit->getProduit()->getBoutique()->getUser()) {
                $messages[] = [
                    'message' => $value->getMessage(),
                    'emetteurId' =>  $value->isEmetteur() == true ? 0 : 1.,

                    'date' =>  $value->getDateEnvoi()->format('Y-m-d'),
                    'heure' =>  $value->getDateEnvoi()->format('H:i'),
                ];
            }
        }
        return new JsonResponse([
            'data' => $messages,




        ], 200);
    }
    /**
     * @Route("/test/general/{indexw}", name="TestSocketGeneral", methods={"GET"})
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
    public function TestSocketGeneral($indexw)
    {


        // $host = 'http://localhost:3000';
        // $first =   $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        // $content = $first->getContent();
        // $index = strpos($content, 0);
        // $res = json_decode(substr($content, $index + 1), true);
        // $sid = $res['sid'];
        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => '40'
        // ]);
        // $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}");

        $data = [

            "indexw" => $indexw,
            "message" => 'Id fugiat ex nulla veniam ea exercitation velit dolor ad. Minim exercitation magna officia ipsum. Nostrud eu officia ipsum pariatur cillum. Non labore amet non sunt ullamco eiusmod veniam laboris. Ea occaecat in excepteur velit commodo esse. Consectetur laborum in amet voluptate pariatur.'

        ];

        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s', json_encode($data))
        // ]);
        $this->myFunction->Socekt_Emit_general($data);

        return $this->json([
            'status'
            =>   $data

        ]);
    }




    public function createNewJWT(UserPlateform $user)
    {
        $token = $this->jwt->create($user);

        $datetime = new \DateTime();
        $datetime->modify('+2592000 seconds');

        $refreshToken = $this->jwtRefresh->create();

        $refreshToken->setUsername($user->getUsername());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($datetime);

        // Validate, that the new token is a unique refresh token
        $valid = false;
        while (false === $valid) {
            $valid = true;
            $errors = $this->validator->validate($refreshToken);
            if ($errors->count() > 0) {
                foreach ($errors as $error) {
                    if ('refreshToken' === $error->getPropertyPath()) {
                        $valid = false;
                        $refreshToken->setRefreshToken();
                    }
                }
            }
        }

        $this->jwtRefresh->save($refreshToken);

        return new JsonResponse([
            'token' => $token,
            'refreshToken' => $refreshToken->getRefreshToken()
        ], 200);
    }




    /**
     * @Route("/dashboard/category", name="categoryNew", methods={"POST"})
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
    public function categoryNew(Request $request, SluggerInterface $slugger)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();





        if (
            empty($data['adminkeySecret'])
            || empty($data['libelle']) || empty($data['description'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
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


        // $file =  $request->files->get('file');
        // if ($file) {
        //     $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //     // this is needed to safely include the file name as part of the URL
        //     $safeFilenameData = $slugger->slug($originalFilenameData);
        //     $newFilenameData  =
        //         $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
        //     $file->move(
        //         $this->getParameter('category_object'),
        //         $newFilenameData
        //     );

        $category = new Category();

        $category->setLibelle($data['libelle'] ?? '');
        $category->setDescription($data['description'] ?? '');
        $category->setLogo($this->myFunction::BACK_END_URL . '/images/default/boutique.png');

        $this->em->persist($category);
        $this->em->flush();

        return
            new JsonResponse(
                [
                    'message'
                    => 'success'
                ],
                200
            );
        // } else {
        //     return
        //         new JsonResponse([
        //             'data'
        //             => [],
        //             'message' => 'Une Erreur est survenue'
        //         ], 203);
        // }
    }

    /**
     * @Route("/dashboard/category/update", name="categoryUpdate", methods={"PATCH"})
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
    public function categoryUpdate(Request $request, SluggerInterface $slugger)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = [
            'category' => $request->get('category'),
            'keySecret' => $request->get('keySecret'),
            'libelle' => $request->get('libelle'),
            'description' => $request->get('description'),

        ];





        if (
            empty($data['keySecret'])


        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $category = $data['category'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);


        if ($user) {
            if ($user->getTypeUser()->getId() == 1) {
                $category = $this->em->getRepository(Category::class)->findOneBy(['category' => $category]);


                $category->setLibelle($data['libelle'] ?? $category->getLibelle());
                $category->setDescription($data['description'] ?? $category->getDescription());
                $file =  $request->files->get('file');
                if ($file) {
                    $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilenameData = $slugger->slug($originalFilenameData);
                    $newFilenameData  =
                        $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('category_object'),
                        $newFilenameData
                    );

                    $category->setLogo($newFilenameData);
                }
                $this->em->persist($category);
                $this->em->flush();

                return
                    new JsonResponse(
                        [
                            'message'
                            => 'success'
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
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }
    /**
     * @Route("/dashboard/category/status/change", name="categoryStatusChange", methods={"PATCH"})
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
    public function categoryStatusChange(Request $request, SluggerInterface $slugger)
    {




        $data = $request->toArray();

        if (
            empty($data['category']) ||
            empty($data['adminkeySecret'])


        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez renseigner les informations correctes'
                ],
                400
            );
        }

        $category  =
            $data['category'];
        $adminkeySecret
            = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
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



        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $category]);
        $category->setStatus(
            !$category->isStatus()
        );
        $this->em->persist($category);
        $this->em->flush();

        return
            new JsonResponse(
                [
                    'message'
                    => 'success'
                ],
                200
            );
    }


    /**
     * @Route("/dashboard/category", name="categoryReadAll", methods={"GET"})
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
    public function categoryReadAll(Request $request)
    {

        $possible = false;
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



        $lCategory = $this->em->getRepository(Category::class)->findAll();

        if ($lCategory) {

            $lC = [];
            foreach ($lCategory  as $cat) {

                $catU =  [
                    'id' => $cat->getId(),
                    'libelle' => $cat->getLibelle(),
                    'description' => $cat->getDescription(),
                    'logo' => $this->myFunction::BACK_END_URL . '/images/category/' . $cat->getLogo(),
                    // 'titre' => $cat->getTitre(), 
                    'status' => $cat->isStatus(),

                ];
                array_push($lC, $catU);
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
     * @Route("/dashboard/category/read/boutique", name="categoryReadBoutique", methods={"GET"})
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
    public function categoryReadBoutique(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;

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
    public function getNewPssw(/* $id */)
    {

        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 7; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }

        $chaine .= '@';
        for ($i = 0; $i < 2; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }


        return $chaine;
    }
}
