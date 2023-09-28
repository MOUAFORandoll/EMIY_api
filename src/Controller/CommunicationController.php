<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\MessageCommunication;
use App\Entity\MessageNegociation;
use App\Entity\Communication;
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

class CommunicationController extends AbstractController
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
     * @Route("/communication/message/new", name="communicationMessage", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function CommunicationMessage(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret']) || empty($data['message']) || empty($data['codeCommunication'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }
        $message
            = $data['message'];
        $keySecret            = $data['keySecret'];
        $codeCommunication            = $data['codeCommunication'];
        $emetteur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $communication = $this->em->getRepository(Communication::class)->findOneBy(['codeCommunication' => $codeCommunication]);


        $messageCommunication = new MessageCommunication();

        $messageCommunication->setEmetteur(($emetteur->getId() ==  $communication->getClient()->getId()));
        $messageCommunication->setInitiateur($emetteur);

        $messageCommunication->setMessage($message);
        $messageCommunication->setCommunication($communication);
        $this->em->persist(
            $messageCommunication
        );
        $this->em->flush();

        $data = [
            'canal'
            =>
            $communication->getCodeCommunication(),
            'data' => [
                'id' =>   $messageCommunication->getId(),
                'message' => $message,
                'codeCom' =>    $communication->getCodeCommunication(),
                'is_service' =>       $messageCommunication->getInitiateur()->getId()
                    == $communication->getClient()->getId() ? 0 : 1,
                'emetteurId' =>  $emetteur->getId(),
                'date' =>  $messageCommunication->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $messageCommunication->getDateEnvoi()->format('H:i'),
            ]
        ];

        $this->myFunction->Socekt_Emit("service_client", $data);
        return new JsonResponse([
            'status' => true,


        ], 200);
    }

    /**
     * @Route("/communication/message/list", name="CommunicationMessageList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function CommunicationMessageList(Request $request)
    {
        $code = $request->query->get('code');

        $communication = $this->em->getRepository(Communication::class)->findOneBy(['codeCommunication' => $code]);
        $messagecommunication = $this->em->getRepository(MessageCommunication::class)->findBy(['communication' => $communication]);
        $messages = [];
        foreach ($messagecommunication as   $value) {

            $messages[] = [
                'id' => $value->getId(),
                'message' => $value->getMessage(),
                'emetteurId' =>       $value->getInitiateur()->getId(),

                'date' =>  $value->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $value->getDateEnvoi()->format('H:i'),
            ];
        }
        return new JsonResponse([
            'data' => $messages,




        ], 200);
    }
}
