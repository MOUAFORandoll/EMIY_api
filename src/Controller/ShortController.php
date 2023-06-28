<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

class ShortController extends AbstractController
{

    private $em;
    private   $serializer;
    private $paginator;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->paginator = $paginator;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/short/read/{index}", name="ShortRead", methods={"GET"})
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
    public function ShortRead($index)
    {

        $pagination = 10;



        $lShortF = [];

        $result = $this->em->getRepository(Short::class)->findAll();
        $lShort = $this->paginator->paginate($result, $index, 12);
        foreach ($lShort as $short) {



            $boutique = $short->getBoutique();

            if ($boutique) {



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
                    ];
                }




                $shortF =  [

                    'id' => $short->getId(),
                    'titre' => $short->getTitre() ?? "Aucun",
                    'description' => $short->getDescription() ?? "Aucun",
                    'status' => $short->isStatus(),
                    'Preview' =>  $short->getPreview(),
                    'src' =>  $short->getSrc(),
                    'date' =>
                    date_format($short->getDateCreated(), 'Y-m-d H:i'),
                    'boutique' =>  $boutiqueU




                ];
                array_push($lShortF, $shortF);
            }
        }


        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,


                ],
                200
            );
    }

    /**
     * @Route("/short/boutique/read", name="ShortBoutiqueRead", methods={"POST"})
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
    public function ShortBoutiqueRead(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $lShortF = [];
        $data = $request->toArray();
        if (empty($data['codeBoutique'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   '
            ], 400);
        }

        // $keySecret = $data['keySecret'];
        $codeBoutique = $data['codeBoutique'];
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);

        $lShort = $this->em->getRepository(Short::class)->findBy(['boutique' => $boutique]);
        foreach ($lShort as $short) {




            $shortF =  [

                'id' => $short->getId(),
                'titre' => $short->getTitre() ?? "Aucun",
                'description' => $short->getDescription() ?? "Aucun",
                'status' => $short->isStatus(),
                'src' =>  $short->getSrc(),
                'Preview' =>  $short->getPreview(),
                'srcBoutique' => 'd',
                'date' =>
                date_format($short->getDateCreated(), 'Y-m-d H:i'),



            ];
            array_push($lShortF, $shortF);
        }

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,


                ],
                200
            );
    }
    /**
     * @Route("/short/new", name="ShortNew", methods={"POST"})
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
    public function ShortNew(Request $request, SluggerInterface $slugger)
    {

        // try {
        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;




        $data = [

            'titre' => $request->get('titre'),
            'description' => $request->get('description'),

            'codeBoutique' => $request->get('codeBoutique'),
        ];

        if (
            empty($data['titre']) || empty($data['description'])


            || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Verifier votre requette',
                    // 'data'=> $data
                ],
                400
            );
        }

        $titre = $data['titre'];
        $description = $data['description'];
        // dd( $this->getParameter( 'shorts_object' ) );

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);

        $file = $request->files->get('file');

        // dd($file);

        if ($file &&   $boutique) {
            $nameFile                 = $this->myFunction->getUniqueNameProduit();
            $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilenameData = $slugger->slug($originalFilenameData);
            $newFilenameData =
                $nameFile . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {

                $file->move(
                    $this->getParameter('shorts_object'),
                    $newFilenameData
                );
                $this->extractImageFromVideoAction(
                    $this->getParameter('shorts_object') . '/' .
                        $newFilenameData,

                    $this->getParameter('shorts_object') . '/' .    $nameFile . '.jpg'


                );
                $short = new Short();
                $short->setSrc($newFilenameData);
                $short->setPreview($nameFile . '.jpg');
                $short->setTitre($titre);
                $short->setDescription(
                    $description
                );
                $short->setBoutique($boutique);
                $this->em->persist($short);
                $this->em->flush();

                return
                    new JsonResponse(
                        [
                            'message'
                            =>  'success'
                        ],
                        200
                    );
            } catch (FileException $e) {
                return
                    new JsonResponse([

                        'status' =>   false,
                        'message' =>   'Vos fichiers ne sont pas valides'

                    ], 203);
            }
        }
        // } catch (\Exception $e) {
        //     // Une erreur s'est produite, annulez la transaction

        //     return new JsonResponse([
        //         'message' => 'Une erreur est survenue',
        //         ' $e' => $e
        //     ], 203);
        // }
    }

    public function extractImageFromVideoAction($videoPath, $imagePath)
    {

        // Exécute la commande FFmpeg
        $command = "ffmpeg -i $videoPath -ss 00:00:01 -vframes 1 $imagePath";
        exec($command);

        // Vérifie si l'image a été correctement extraite
        if (file_exists($imagePath)) {
            // Retourne l'image extraite en tant que réponse
            return new Response(file_get_contents($imagePath), 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="image.jpg"'
            ]);
        } else {
            // Gère l'erreur si l'image n'a pas pu être extraite
            return new Response('Erreur lors de l\'extraction de l\'image', 500);
        }
    }
}
/****
 * 
 */
