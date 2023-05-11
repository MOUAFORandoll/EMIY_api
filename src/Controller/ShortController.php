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

class ShortController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/short/read", name="ShortRead", methods={"GET"})
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
    public function ShortRead(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;

         

        $lShortF = [];

        $lShort = $this->em->getRepository(Short::class)->findAll();
        foreach ($lShort as $short) {
            $boutique = $short->getBoutique();
            if ($boutique) {
                $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                $limgB = [];

                foreach ($lBo  as $bo) {
                    $limgB[]
                        =  /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/boutiques/' . $bo->getSrc();
                }
                if (empty($limgB)) {
                    $limgB[] =     /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/default/boutique.png';
                }

                $shortF =  [

                    'id' => $short->getId(),
                    'titre' => $short->getTitre() ?? "Aucun",
                    'description' => $short->getDescription() ?? "Aucun",
                    'status' => $short->isStatus(),
                    'src' => /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/videos/shorts/' . $short->getSrc(),
                    'date' =>
                    date_format($short->getDateCreated(), 'Y-m-d H:i'),
                    'srcBoutique' =>   $limgB[count($limgB) - 1]




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
                'src' => /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/videos/shorts/' . $short->getSrc(),
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


        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);

        $file = $request->files->get('file');



        if ($file) {
            $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilenameData = $slugger->slug($originalFilenameData);
            $newFilenameData =
                $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {

                $file->move(
                    $this->getParameter('shorts_object'),
                    $newFilenameData
                );
                $short = new Short();
                $short->setSrc($newFilenameData);
                $short->setTitre($titre);
                $short->setDescription(
                    $description
                );
                $short->setBoutique($boutique);
                $this->em->persist($short);

                $imagePresent = true;
            } catch (FileException $e) {
                return
                    new JsonResponse([

                        'status' =>   false,
                        'message' =>   'Vos fichiers ne sont pas valides'

                    ], 203);
            }
        }

        $this->em->flush();

        return
            new JsonResponse(
                [
                    'message'
                    =>  'success'
                ],
                200
            );
    }
}
/****
 * 
 */
