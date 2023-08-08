<?php

namespace App\Controller;

use App\Entity\Boutique;
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
use Knp\Component\Pager\PaginatorInterface;


class ProduitController extends AbstractController
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
     * @Route("/produit/new", name="produitNew", methods={"POST"})
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
    public function produitNew(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;




        $data = [
            'keySecret' => $request->get('keySecret'),
            'negociable' => $request->get('negociable'),
            'titre' => $request->get('titre'),
            'description' => $request->get('description'),
            'prixUnitaire' => $request->get('prixUnitaire'),
            'quantite' => $request->get('quantite'),
            'idCategory'
            => $request->get('idCategory'),
            'codeBoutique' => $request->get('codeBoutique'),
        ];

        if (
            empty($request->get('countImage')) ||   empty($data['keySecret'])
            || empty($data['titre']) || empty($data['description'])

            || empty($data['prixUnitaire']) || empty($data['quantite'])
            || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Verifier votre requette'
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);
        $commission = $this->em->getRepository(Commission::class)->findOneBy(['id' => 1]);


        $imagePresent = false;

        if ($boutique->getUser() ==  $user) {
            $produit = new Produit();
            $produit->setTitre($data['titre'] ?? '');
            $produit->setNegociable($data['negociable'] ?? false);
            $produit->setDescription($data['description'] ?? '');
            $produit->setQuantite($data['quantite'] ?? 1000);
            $produit->setTaille($data['taille'] ?? 0);
            $price = ($data['prixUnitaire']  +
                ($data['prixUnitaire']  * $commission->getPourcentageProduit() / 100
                    + $commission->getFraisLivraisonProduit()

                )
            );
            $produit->setPrixUnitaire($data['prixUnitaire'] ? $price : 1);
            $produit->setBoutique($boutique);
            $produit->setCodeProduit($this->getUniqueCodeProduit());

            for ($i = 0; $i < $request->get('countImage'); $i++) {
                $file = $request->files->get('file' . $i);
                if ($file) {
                    $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilenameData = $slugger->slug($originalFilenameData);
                    $newFilenameData =
                        $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('produits_object'),
                            $newFilenameData
                        );
                        $produitObject = new ProduitObject();
                        $produitObject->setSrc($newFilenameData);
                        $produitObject->setProduit($produit);
                        $this->em->persist($produit);
                        $this->em->persist($produitObject);

                        $imagePresent = true;
                    } catch (FileException $e) {
                        return
                            new JsonResponse([

                                'status' =>   false,
                                'message' =>   'Vos fichiers ne sont pas valides'

                            ], 203);
                    }
                }
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
                        'message' =>   'Ajoutez des images a votre produit'

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
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }


    /**
     * @Route("/produit/read/all", name="produitReadAll", methods={"GET"})
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
    public function produitReadAll(Request $request)
    {

        $possible = false;
        // $keySecret = $request->get('keySecret')
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);



        $lProduit = $this->em->getRepository(Produit::class)->findAll();

        if ($lProduit) {

            $lP = [];
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
                array_push($lP, $produit);
            }
            // $listProduits = $serializer->serialize($lP, 'json');

            return
                new JsonResponse(
                    [
                        'data'
                        =>  $lP,

                        'statusCode' => 200

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
     * @Route("/produit/read/client", name="produitReadclient", methods={"GET"})
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
    public function produitReadclient(Request $request)
    {
        $page =
            $request->get('page');

        $result = $this->em->getRepository(Produit::class)->findAll();
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);
        $lProduit = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        $lP = [];
        if ($lProduit) {
            foreach ($lProduit as $produit) {

                if ($produit->isStatus() && $produit->getQuantite() > 0) {
                    $lsImgP = [];
                    $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                    foreach ($lProduitO  as $produit0) {
                        $lsImgP[]
                            = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                    }



                    $produitU =  [
                        'id' => $produit->getId(),
                        'codeProduit' => $produit->getCodeProduit(),
                        'boutique' => $produit->getBoutique()->getTitre(),
                        'description' => $produit->getDescription(),
                        'like' => $this->myFunction->isLike_Produit($produit->getId()),     'titre' => $produit->getTitre(),
                        'islike' =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),
                        'quantite' => $produit->getQuantite(),
                        'prix' => $produit->getPrixUnitaire(),
                        'status' => $produit->isStatus(),
                        // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
                        'images' => $lsImgP

                    ];
                    array_push($lP, $produitU);
                }
            }
        }
        // $listProduits = $serializer->serialize($lP, 'json');

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lP,

                    'statusCode' => 200

                ],
                200
            );
    }

    /**
     * @Route("/produit/read/popular", name="produitReadPopular", methods={"GET"})
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
    public function produitReadPopular(Request $request,)
    {
        $page =
            $request->get('page');
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $result = $this->em->getRepository(Produit::class)->findAll();
        $lProduit = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        $lP = [];

        foreach ($lProduit as $produit) {
            # code... 

            if ($produit->isStatus() && $produit->getQuantite() > 0) {
                $lsImgP    = [];
                $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                foreach ($lProduitO as $produit0) {
                    $lsImgP[]
                        = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                }



                $produitU = [

                    'id' => $produit->getId(),
                    'like' => $this->myFunction->isLike_produit($produit->getId()),
                    'islike' =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),
                    'codeProduit' => $produit->getCodeProduit(),
                    'boutique' => $produit->getBoutique()->getTitre(),
                    'description' => $produit->getDescription(),
                    'titre' => $produit->getTitre(),
                    'negociable' => $produit->isNegociable(), 'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
                    'quantite' => $produit->getQuantite(),
                    'prix' => $produit->getPrixUnitaire(),
                    'status' => $produit->isStatus(),
                    // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
                    'images' => $lsImgP

                ];
                array_push($lP, $produitU);
            }
            // }


            // if (count($lP) % 2 != 0) {
            //     $indexP  = 0;
            //     $produit = $lProduit[$indexP];

            //     if ($produit->isStatus() && $produit->getQuantite() > 0) {
            //         $lsImgP = [];
            //         $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
            //         foreach ($lProduitO  as $produit0) {
            //             $lsImgP[]
            //                 = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
            //         }



            //         $produitU =  [
            //             'id' => $produit->getId(),
            //             'like' => $this->myFunction->isLike_produitProduit($produit->getId()),
            //             'codeProduit' => $produit->getCodeProduit(),
            //             'boutique' => $produit->getBoutique()->getTitre(),
            //             'description' => $produit->getDescription(),
            //             'titre' => $produit->getTitre(),
            //                     'negociable' => $produit->isNegociable(), 'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
            //             'quantite' => $produit->getQuantite(),
            //             'prix' => $produit->getPrixUnitaire(),
            //             'status' => $produit->isStatus(),
            //             // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
            //             'images' => $lsImgP

            //         ];
            //         array_push($lP, $produitU);
            //     }
            // }
            // $listProduits = $serializer->serialize($lP, 'json');
        }
        return
            new JsonResponse(
                [
                    'data'
                    => $lP,

                    'statusCode' => 200

                ],
                200
            );
    }

    /**
     * @Route("/produit/read/boutique", name="produitReadBoutique", methods={"POST"})
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
    public function produitReadBoutique(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            empty($data['keySecret']) || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $codeBoutique = $data['codeBoutique'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);

        if ($boutique) {


            if ($boutique->getUser() ==  $user) {
                $lP = [];
                foreach ($boutique->getProduits()  as $produit) {
                    if ($produit->isStatus() && $produit->getQuantite() > 0) {
                        $produit =  [
                            'like' => $this->myFunction->isLike_Produit($produit->getId()),
                            // isLike =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),
                            'codeProduit' => $produit->getCodeProduit(),
                            'user' => $produit->getUser()->getNom() . ' ' . $produit->getUser()->getPrenom(),
                            'description' => $produit->getDescription(),
                            'titre' => $produit->getTitre(),
                            'negociable' => $produit->isNegociable(),  'quantite' => $produit->getQuantite(),
                            'prix' => $produit->getPrixUnitaire(),
                            'status' => $produit->isStatus(),
                            'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0


                        ];
                        array_push($lP, $produit);
                    }
                }
                // $listProduits = $serializer->serialize($lP, 'json');

                return
                    new JsonResponse(
                        [
                            'data'
                            =>  $lP
                        ],
                        200
                    );
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
                    'message' => 'Boutique inexistante'
                ], 203);
        }
    }


    /**
     * @Route("/produit/update", name="produitUpdate", methods={"POST"})
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
    public function produitUpdate(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            /*    empty($data['keySecret']) || */
            empty($data['idProduit'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        // $keySecret = $data['keySecret'];
        $id = $data['idProduit'];
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        // $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['user' => $user]);
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);

        if ($produit) {
            $commission = $this->em->getRepository(Commission::class)->findOneBy(['id' => 1]);


            // if ($produit->getBoutiques()[0]->getUser() ==  $user) {

            $produit->setTitre($data['titre'] ?? $produit->getTitre());
            $produit->setDescription($data['description'] ?? $produit->getDescription());
            $produit->setQuantite($data['quantite'] ?? $produit->getQuantite());

            $price = ($data['prixUnitaire']  +
                ($data['prixUnitaire']  * $commission->getPourcentageProduit() / 100
                    + $commission->getFraisLivraisonProduit()

                )
            );
            $produit->setPrixUnitaire($data['prixUnitaire'] ? $price : $produit->getPrixUnitaire());

            $this->em->persist($produit);
            $this->em->flush();
            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success'
                    ],
                    200
                );
            // } else {
            //     return
            //         new JsonResponse([
            //             'data'
            //             => [],
            //             'message' => 'Aucune authorisation'
            //         ], 203);
            // }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Produit introuvable'
                ], 203);
        }
    }


    /**
     * @Route("/produit/state", name="produitState", methods={"POST"})
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
    public function produitState(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            /*     empty($data['keySecret']) ||  */
            empty($data['idProduit'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }


        // $keySecret = $data['keySecret'];
        $id = $data['idProduit'];
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        // $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['user' => $user]);
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);

        if ($produit) {



            // if ($produit->getBoutiques()[0]->getUser() ==  $user) {

            $produit->setStatus(false);

            $this->em->persist($produit);
            $this->em->flush();
            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success'
                    ],
                    200
                );
            // } else {
            //     return
            //         new JsonResponse([
            //             'data'
            //             => [],
            //             'message' => 'Aucune authorisation'
            //         ], 203);
            // }
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Produit introuvable'
                ], 203);
        }
    }









    /**
     * @Route("/produit/image/add", name="produtImageAdd", methods={"POST"})
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
    public function produtImageAdd(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $data = [
            'idProduit' => $request->get('idProduit'),
            'keySecret' => $request->get('keySecret'),

            'codeBoutique' => $request->get('codeBoutique'),
        ];
        if (
            /*     empty($data['keySecret']) ||  */
            empty($data['idProduit']) ||
            empty($data['keySecret'])
            || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez  reessayer'
                ],
                400
            );
        }

        $keySecret = $data['keySecret'];
        $id = $data['idProduit'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);


        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);
        $imagePresent = false;
        if ($produit && $boutique && $user) {
            if ($boutique  ==  $produit->getBoutique()) {
                if ($boutique->getUser() ==  $user) {


                    $file = $request->files->get('file');
                    if ($file) {
                        $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilenameData = $slugger->slug($originalFilenameData);
                        $newFilenameData =
                            $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
                        try {
                            $file->move(
                                $this->getParameter('produits_object'),
                                $newFilenameData
                            );
                            $produitObject = new ProduitObject();
                            $produitObject->setSrc($newFilenameData);
                            $produitObject->setProduit($produit);
                            $this->em->persist($produit);
                            $this->em->persist($produitObject);

                            $imagePresent = true;
                        } catch (FileException $e) {
                            return
                                new JsonResponse([

                                    'status' =>   false,
                                    'message' =>   'Vos fichiers ne sont pas valides'

                                ], 203);
                        }
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
     * @Route("/produit/image/update", name="produtImageUpdate", methods={"POST"})
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
    public function produtImageUpdate(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $data = [
            'idProduitObject' => $request->get('idProduitObject'),
            'keySecret' => $request->get('keySecret'),

            'codeBoutique' => $request->get('codeBoutique'),
        ];

        if (
            empty($data['idProduitObject']) ||    empty($data['keySecret'])
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
        $produitObject = $this->em->getRepository(ProduitObject::class)->findOneBy(['id' => $data['idProduitObject']]);
        if ($boutique->getUser() ==  $user && $produitObject) {
            $file =  $request->files->get('file');
            if ($file) {
                $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilenameData = $slugger->slug($originalFilenameData);
                $newFilenameData =
                    $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('produits_object'),
                        $newFilenameData
                    );

                    $produitObject->setSrc($newFilenameData);

                    $this->em->persist($produitObject);

                    $imagePresent = true;
                } catch (FileException $e) {
                    return
                        new JsonResponse([

                            'status' =>   false,
                            'message' =>   'Vos fichiers ne sont pas valides'

                        ], 203);
                }
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
     * @Route("/produit/multiple/new", name="produitMultipleNew", methods={"GET"})
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
    public function produitMultipleNew(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $data = [
            'keySecret' => $request->get('keySecret'),
            'titre' => $request->get('titre'),
            'description' => $request->get('description'),
            'prixUnitaire' => $request->get('prixUnitaire'),
            'quantite' => $request->get('quantite'),
            'idCategory'
            => $request->get('idCategory'),
            'idBoutique' => $request->get('idBoutique'),
        ];

        if (
            empty($data['keySecret'])
            || empty($data['titre']) || empty($data['description'])

            || empty($data['prixUnitaire']) || empty($data['quantite'])
            || empty($data['idCategory']) || empty($data['idBoutique'])
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

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['id' => $data['idBoutique']]);


        $imagePresent = false;

        if ($boutique->getUser() ==  $user) {
            $produit = new Produit();
            $produit->setTitre($data['titre'] ?? '');
            $produit->setDescription($data['description'] ?? '');
            $produit->setQuantite($data['quantite'] ?? 1000);
            $produit->setPrixUnitaire($data['prixUnitaire'] ?? 1);
            $produit->setBoutique($boutique);

            $produit->setCodeProduit($this->getUniqueCodeProduit());

            for ($i = 0; $i < 5; $i++) {
                $file =  $request->files->get('file' . $i);
                if ($file) {
                    $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilenameData = $slugger->slug($originalFilenameData);
                    $newFilenameData =
                        $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();

                    $file->move(
                        $this->getParameter('produits_object'),
                        $newFilenameData
                    );
                    $produitObject = new ProduitObject();
                    $produitObject->setSrc($newFilenameData);
                    $produitObject->setProduit($produit);
                    $this->em->persist($produit);
                    $this->em->persist($produitObject);

                    $imagePresent = true;
                }
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
                        'message' =>   'Ajoutez des images a votre produit'

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
                    'data'
                    => [],
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }
}
