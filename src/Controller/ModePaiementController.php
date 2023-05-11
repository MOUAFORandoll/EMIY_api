<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\ProduitObject;
use App\Entity\UserPlateform;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

class ModePaiementController extends AbstractController
{

    private $em;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction

    ) {
        $this->em = $em;

        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/modepaiement/read", name="modePaiementRead", methods={"GET"})
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
    public function modePaiementRead(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $lM = [];

        $lmode = $this->em->getRepository(ModePaiement::class)->findAll();
        foreach ($lmode as $mode) {


            $img
                =   /*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] .
                ($mode->getId() == 1 ? '/images/payement/om.png'
                    : ($mode->getId() == 2 ?
                        '/images/payement/momo.png'
                        : ($mode->getId() == 3 ?
                            '/images/payement/paycard.png' : '')));

            $modeF =  [

                'id' => $mode->getId(),
                'libelle' => $mode->getLibelle() ?? "Aucun",
                // 'status' => $mode->isStatus(), 
                'img' => $img,



            ];
            array_push($lM, $modeF);
        }

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lM,
                    'statusCode' => 200

                ],
                200
            );
    }
}
