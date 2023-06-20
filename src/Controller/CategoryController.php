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
     * @Route("/category/new", name="categoryNew", methods={"POST"})
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
        $data = [
            'keySecret' => $request->get('keySecret'),
            'libelle' => $request->get('libelle'),
            'description' => $request->get('description'),

        ];





        if (
            empty($data['keySecret'])
            || empty($data['libelle']) || empty($data['description'])

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


        if ($user) {
            if ($user->getTypeUser()->getId() == 1) {

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

                    $category = new Category();

                    $category->setLibelle($data['libelle'] ?? '');
                    $category->setDescription($data['description'] ?? '');
                    $category->setLogo($newFilenameData);

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
     * @Route("/category/update", name="categoryUpdate", methods={"PATCH"})
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
     * @Route("/category/status/change", name="categoryStatusChange", methods={"PATCH"})
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
            empty($data['keySecret'])


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
        $keySecret
            = $data['keySecret'];




        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);


        if ($user) {
            if ($user->getTypeUser()->getId() == 1) {
                $category = $this->em->getRepository(Category::class)->findOneBy(['category' => $category]);
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
                        'logo' => 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/category/' . $category->getLogo(),
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
     * @Route("/category/read/all", name="categoryReadAll", methods={"GET"})
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




        $lCategory = $this->em->getRepository(Category::class)->findAll();

        if ($lCategory) {

            $lC = [];
            foreach ($lCategory  as $cat) {

                $catU =  [
                    'id' => $cat->getId(),
                    'libelle' => $cat->getLibelle(),
                    'description' => $cat->getDescription(),
                    'logo' => 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/category/' . $cat->getLogo(),
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
     * @Route("/category/read/boutique", name="categoryReadBoutique", methods={"GET"})
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
