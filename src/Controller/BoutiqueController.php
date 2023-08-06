<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Communication;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
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

class BoutiqueController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,

        MyFunction  $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }
    /**
     * @Route("/boutique", name="app_boutique")
     */
    public function index(): Response
    {
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
        ]);
    }

    public function getUniqueCodeBoutique()
    {


        $chaine = 'boutique';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $chaine]);
        if ($ExistCode) {
            return
                $this->getUniqueCodeBoutique();
        } else {
            return $chaine;
        }
    }
    /**
     * @Route("/boutique/update", name="updateBoutique", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateBoutique(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['codeBoutique'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer '
            ], 400);
        }
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);
        if (!$boutique) {
            return new JsonResponse([
                'message' => 'Desolez l\'a boutique en question n\'existe pas'
            ], 400);
        }

        if (!empty($data['titre'])) {
            $boutique->setTitre($data['titre']);
        }

        if (!empty($data['description'])) {
            $boutique->setDescription($data['description']);
        }
        // if (!empty($data['email'])) {
        //     $boutique->setEmail($data['email']);
        // }


        // if (!empty($data['phone'])) {
        //     $boutique->setPhone($data['phone']);
        // }


        $this->em->persist($boutique);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'success',

        ], 200);
    }

    /**
     * @Route("/boutique/user/new", name="boutiqueUserNew", methods={"POST"})
     *  @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     */
    public function boutiqueUserNew(Request $request, SluggerInterface $slugger)
    {

        try {
            $data = [
                'keySecret' => $request->get('keySecret'),

                'description' => $request->get('description'),
                'idCategory' => $request->get('idCategory'),
                'titre' => $request->get('titre'),
                'ville' => $request->get('ville'),
                'longitude' =>
                $request->get('longitude'),
                'latitude' => $request->get('latitude'),
            ];
            // return new JsonResponse([

            //     'd' => $data
            // ], 400);
            if (empty($data['titre']) ||/*  $request->files->get('file')    || */ empty($data['idCategory']) || empty($data['keySecret']) || empty($data['description'])) {
                return new JsonResponse([
                    'message' => 'Veuillez recharger la page et reessayer   ',

                ], 400);
            }
            $description = $data['description'];
            $titre = $data['titre'];
            $keySecret = $data['keySecret'];
            $ville = $data['ville'];
            $longitude = $data['longitude'];
            $latitude = $data['latitude'];
            $codeBoutique = $this->getUniqueCodeBoutique();
            $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
            if (!$user) {
                return new JsonResponse([
                    'message' => 'Compte introuvable'

                ], 203);
            }
            $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['titre' => $titre]);
            $boutique1 = $this->em->getRepository(Boutique::class)->findOneBy(['user' => $user]);
            if ($boutique1) {
                return new JsonResponse([
                    'message' => 'Vous possedez deja une boutique'

                ], 203);
            }
            $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $data['idCategory']]);
            if (!$boutique) {


                if (!empty($longitude) && !empty($latitude)) {
                    $localisation = new Localisation();
                    $localisation->setVille(
                        $ville
                    );
                    $localisation->setLongitude($longitude);
                    $localisation->setLatitude($latitude);
                    $this->em->persist($localisation);
                }

                $boutique = new Boutique();

                $boutique->setUser($user);
                $boutique->setDescription($description);
                $boutique->setTitre($titre);

                $boutique->setCategory($category);
                $boutique->setCodeBoutique($codeBoutique);


                if (!empty($longitude) && !empty($latitude)) {
                    $boutique->setLocalisation($localisation);
                }
                $this->em->persist($boutique);

                $file =  $request->files->get('file');
                if ($file) {
                    $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilenameData = $slugger->slug($originalFilenameData);
                    $newFilenameData =
                        $this->myFunction->getUniqueNameBoutiqueImg() . '.' . $file->guessExtension();

                    $file->move(
                        $this->getParameter('boutiques_object'),
                        $newFilenameData
                    );
                    $boutiqueO = new BoutiqueObject();

                    $boutiqueO->setSrc($newFilenameData);
                    $boutiqueO->setBoutique($boutique);
                    $this->em->persist($boutiqueO);

                    // $imagePresent = true;
                }
                $this->em->flush();
                return new JsonResponse([
                    'message' => 'Boutique cree avec success',

                    'id' =>  $boutique->getId()

                ], 200);
            } else {
                return new JsonResponse([
                    'message' => 'Ce nom de boutique est deja utilise'

                ], 203);
            }
        } catch (\Exception $e) {
            // Une erreur s'est produite, annulez la transaction

            return new JsonResponse([
                'message' => 'Une erreur est survenue'
            ], 203);
        }
    }



    /**
     * @Route("/boutique/localisation", name="boutiqueLocalisation", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function boutiqueLocalisation(Request $request)
    {
        $data = $request->toArray();
        if (
            empty($data['codeBoutique']) || empty($data['keySecret'])            || empty($data['ville'])

            || empty($data['longitude'])
            || empty($data['latitude'])
        ) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        $ville = $data['ville'];
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];
        $localisation = new Localisation();
        $localisation->setVille(
            $ville
        );
        $localisation->setLongitude($longitude);
        $localisation->setLatitude($latitude);
        $this->em->persist($localisation);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);
        if ($user  == $boutique->getUser()) {


            $boutique->setLocalisation($localisation);



            $this->em->persist($boutique);
            $this->em->flush();

            return new JsonResponse([
                'message' => 'localisation mise a jour',

                'id' =>  $boutique->getId()

            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Echec operation. Contacter un administrateur',



            ], 203);
        }
    }



    /**
     * @Route("/boutique/update", name="boutiqueUpdate", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function boutiqueUpdate(Request $request)
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

        if ($user == $boutique->getUser()) {
            $boutique->setDescription($data['description'] ?? $boutique->getDescription());
            $boutique->setTitre($data['titre'] ??  $boutique->getTitre());

            $boutique->setCodeBoutique($codeBoutique);



            $this->em->persist($boutique);
            $this->em->flush();

            return new JsonResponse([
                'message' => 'Boutique modifiee avec success',

                'id' =>  $boutique->getId()

            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Cette boutique ne vous appartient pas'

            ], 203);
        }
    }


    /**
     * @Route("boutique/read/all", name="boutiqueReadAll", methods={"GET"})
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
    public function boutiqueReadAll(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);



        $lBoutique = $this->em->getRepository(Boutique::class)->findAll();
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        if ($lBoutique) {

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
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucune Boutique'
                ], 200);
        }
    }

    /**
     * @Route("/boutique/read/user", name="boutiqueReadForUser", methods={"GET"})
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
    public function boutiqueReadForUser(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;
        if (empty($request->get('keySecret'))) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayerveuillez preciser votre keySecret '
            ], 400);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        if ($user) {



            $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['user' => $user]);

            if ($boutique) {
                $listProduit = [];
                foreach ($boutique->getProduits()  as $produit) {
                    if ($produit->isStatus()) {
                        $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                        $lsImgP = [];

                        foreach ($lProduitO  as $produit0) {
                            $lsImgP[]
                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . 'images/produits/' . $produit0->getSrc()];
                        }
                        $listProduit[] = [
                            'id' => $produit->getId(), 'codeProduit' => $produit->getCodeProduit(),
                            'titre' => $produit->getTitre(), 'quantite' => $produit->getQuantite(),
                            'prix' => $produit->getPrixUnitaire(),
                            'negociable' => $produit->isNegociable(),    'status' => $produit->isStatus(),
                            'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
                            'description' => $produit->getDescription(),
                            'images' => $lsImgP

                        ];
                    }
                }

                $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                $limgB = [];

                foreach ($lBo  as $bo) {
                    $limgB[]
                        = ['id' => $bo->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                }
                if (empty($limgB)) {
                    $limgB[]
                        = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                }
                $boutique =  [
                    'codeBoutique' => $boutique->getCodeBoutique(),
                    'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                    'description' => $boutique->getDescription(),
                    'titre' => $boutique->getTitre(),
                    'status' => $boutique->isStatus(),

                    'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                    'produits' => $listProduit,
                    'images' => $limgB,
                    'localisation' =>
                    $boutique->getLocalisation() ? [
                        'ville' =>
                        $boutique->getLocalisation()->getVille(),

                        'longitude' =>
                        $boutique->getLocalisation()->getLongitude(),
                        'latitude' =>
                        $boutique->getLocalisation()->getLatitude(),
                    ] : [
                        'ville' =>
                        'Inconnu',

                        'longitude' =>
                        0,
                        'latitude' =>
                        0,
                    ]


                ];
                return
                    new JsonResponse(
                        [
                            'exist' => true,
                            'data'
                            =>    $boutique

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
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Utilisateur introuvable'
            ], 400);
        }
    }
    /**
     * @Route("/boutique/read/info", name="boutiqueReadInfo", methods={"POST"})
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
    public function boutiqueReadInfo(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();

        if (empty($data['codeBoutique']) || empty($data['adminkeySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer veuillez preciser votre code Boutique '
            ], 400);
        }
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);

        if ($admin) {
            if ($admin->getTypeUser()->getId() == 1) {



                $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);

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
                                'id' => $produit->getId(), 'codeProduit' => $produit->getCodeProduit(),
                                'titre' => $produit->getTitre(), 'quantite' => $produit->getQuantite(),
                                'prix' => $produit->getPrixUnitaire(),
                                'status' => $produit->isStatus(),
                                'negociable' => $produit->isNegociable(), 'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
                                'description' => $produit->getDescription(),
                                'images' => $lsImgP

                            ];
                        }
                    }

                    $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                    $limgB = [];

                    foreach ($lBo  as $bo) {
                        $limgB[]
                            = ['id' => $bo->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                    }
                    if (empty($limgB)) {
                        $limgB[]
                            = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                    }
                    $commande = $this->comandeReadH($data['codeBoutique']);
                    $boutique =  [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription(),
                        'titre' => $boutique->getTitre(),
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),

                        'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                        'produits' => $listProduit,
                        'images' => $limgB,
                        'commandes' => $commande,
                        'localisation' =>
                        $boutique->getLocalisation() ? [
                            'ville' =>
                            $boutique->getLocalisation()->getVille(),

                            'longitude' =>
                            $boutique->getLocalisation()->getLongitude(),
                            'latitude' =>
                            $boutique->getLocalisation()->getLatitude(),
                        ] : [
                            'ville' =>
                            'Inconnu',

                            'longitude' =>
                            0,
                            'latitude' =>
                            0,
                        ]


                    ];
                    return
                        new JsonResponse(
                            [
                                'exist' => true,
                                'data'
                                =>    $boutique

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
            } else {
                return new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Utilisateur introuvable'
                ], 400);
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
     * @Route("/boutique/read/produit", name="boutiqueReadProduit", methods={"GET"})
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
    public function boutiqueReadProduit(Request $request)
    {


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;

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
                        'id' => $produit->getId(), 'codeProduit' => $produit->getCodeProduit(),
                        'titre' => $produit->getTitre(), 'quantite' => $produit->getQuantite(),
                        'prix' => $produit->getPrixUnitaire(),
                        'description' => $produit->getDescription(),
                        'status' => $produit->isStatus(),
                        'like' => $this->myFunction->isLike_Produit($produit->getId()),
                        'islike' =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),

                        'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
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
     * @Route("/boutique/image/new", name="boutiqueImage", methods={"POST"})
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
    public function boutiqueImage(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $data = [
            'keySecret' => $request->get('keySecret'),

            'codeBoutique' => $request->get('codeBoutique'),
        ];

        if (
            empty($data['keySecret'])
            || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);

        $imagePresent = false;

        if ($boutique->getUser() ==  $user) {
            $file =  $request->files->get('file');
            if ($file) {
                $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilenameData = $slugger->slug($originalFilenameData);
                $newFilenameData =
                    $this->myFunction->getUniqueNameBoutiqueImg() . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('boutiques_object'),
                    $newFilenameData
                );
                $boutiqueO = new BoutiqueObject();

                $boutiqueO->setSrc($newFilenameData);
                $boutiqueO->setBoutique($boutique);
                $this->em->persist($boutiqueO);

                $imagePresent = true;
            }
            if ($imagePresent) {
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

                        'status' =>   false,
                        'message' =>   'Ajoutez une image'

                    ], 203);
            }
            // try {

            // } catch (Exception $e) {
            //     return
            //         new JsonResponse([

            //             'status' =>   false,
            //             'message' =>   'Une erreur est survenue' . $e

            //         ], 203);
            // }
        } else {
            return
                new JsonResponse([
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }



    public function comandeReadH($codeBoutique)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;





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

                                        $lsImgP = [];
                                        $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                        foreach ($lProduitO  as $produit0) {
                                            $lsImgP[]
                                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                        }

                                        $com =  [
                                            // 'codeProduit' => $produit->getCodeProduit(),
                                            'codeCommande' => $commande->getCodeCommande(),
                                            'titre' => $produit->getTitre(),
                                            'prix' => $produit->getPrixUnitaire(),
                                            'quantite' => $pp->getQuantite(),
                                            'status' => $pp->isStatus() == 1 ? 'Vendu' : "En cours",

                                            'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                                            'photo' => $lsImgP[0]
                                        ];
                                        array_push($lP, $com);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return
            $lP;
    }
}
