<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\MessageNegociation;
use App\Entity\NegociationProduit;
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

class NegociationController extends AbstractController
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
     * @Route("/negociation/start", name="negociation", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociation(Request $request)
    {
        $data = $request->toArray();

        $codeNegociation = $this->myFunction->getCodeNegociation();

        if (empty($data['keySecret'])  || empty($data['codeProduit'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }

        $keySecret            = $data['keySecret'];
        $codeProduit            = $data['codeProduit'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $codeProduit]);
        $message            = 'J\'aimerais negocier le prix de ce produit : ' .
            $produit->getTitre();
        $negociationProduit = new NegociationProduit();
        $negociationProduit->setProduit($produit);
        $negociationProduit->setInitiateur($user);
        $negociationProduit->setCodeNegociation($codeNegociation);
        $this->em->persist(
            $negociationProduit
        );
        $this->em->flush();
        $messageProduit = new MessageNegociation();
        $messageProduit->setEmetteur(true);
        $messageProduit->setMessage($message);
        $messageProduit->setNegociation($negociationProduit);
        $this->em->persist(
            $messageProduit
        );
        $this->em->flush();

        $data = [
            'canal' =>
            $produit->getBoutique()->getCodeBoutique(),
            'data' => [
                'typeEcoute' => 1,
                'canalNegociation'
                => $codeNegociation,
                'message' =>
                $message,

                'date' =>  $messageProduit->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $messageProduit->getDateEnvoi()->format('H:i'),
            ]
        ];

        $finalData = [
            'canal'
            =>  $codeNegociation,
            'message' => $message,
            'emetteurId' => $user->getId(),

            'date' =>  $messageProduit->getDateEnvoi()->format('Y-m-d'),
            'heure' =>  $messageProduit->getDateEnvoi()->format('H:i'),

        ];

        $this->myFunction->Socekt_Emit('boutique', $data);
        return new JsonResponse([
            'data' => $finalData,


        ], 200);
    }


    /**
     * @Route("/negociation/message/new", name="negociationMessage", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociationMessage(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret']) || empty($data['message']) || empty($data['codeNegociation'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }
        $message
            = $data['message'];
        $keySecret            = $data['keySecret'];
        $codeNegociation            = $data['codeNegociation'];
        $emetteur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findOneBy(['codeNegociation' => $codeNegociation]);


        $messageProduit = new MessageNegociation();
        $boutiqueId
            = $negociationProduit->getProduit()->getBoutique()->getUser()->getId();
        $messageProduit->setEmetteur(!($emetteur->getId() == $boutiqueId));

        $messageProduit->setMessage($message);
        $messageProduit->setNegociation($negociationProduit);
        $this->em->persist(
            $messageProduit
        );
        $this->em->flush();

        $data = [
            'canal'
            =>  $negociationProduit->getCodeNegociation(),
            'data' => [
                'message' => $message,
                'emetteurId' =>  $emetteur->getId(),

                'date' =>  $messageProduit->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $messageProduit->getDateEnvoi()->format('H:i'),
            ]
        ];



        $this->myFunction->Socekt_Emit("negociation", $data);
        return new JsonResponse([
            'status' => true,


        ], 200);
    }
    /**
     * @Route("/negociation/list", name="negociationList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociationList(Request $request)
    {

        $keySecret = $request->query->get('keySecret');

        if (empty($keySecret)) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   ',

            ], 400);
        }


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findBy(['initiateur' => $user]);
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
                    'src_produit' =>/*  $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME'] */ 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/produits/' .  $value->getProduit()->getProduitObjects()[0]->getSrc(),
                    'last_message' => ($lastElement)->getMessage(),
                    'date' =>  $value->getDateCreated()->format('H:i')
                ];
            }
        }
        return new JsonResponse([
            'data' => $negociations,



        ], 200);
    }
    /**
     * @Route("/negociation/message/list", name="negociationMessageList", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociationMessageList(Request $request)
    {
        $code = $request->query->get('code');
        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findOneBy(['codeNegociation' => $code]);
        $messageNegociationProduit = $this->em->getRepository(MessageNegociation::class)->findBy(['negociation' => $negociationProduit]);
        $messages = [];
        foreach ($messageNegociationProduit as   $value) {

            $messages[] = [
                'message' => $value->getMessage(),
                'emetteurId' =>  $value->isEmetteur() == true ?     $negociationProduit->getInitiateur()->getId() :
                    $negociationProduit->getProduit()->getBoutique()->getUser()->getId(),
           
                'date' =>  $value->getDateEnvoi()->format('Y-m-d'),
                'heure' =>  $value->getDateEnvoi()->format('H:i'),
            ];
        }
        return new JsonResponse([
            'data' => $messages,




        ], 200);
    }
    /**
     * @Route("/negociation/produit/set", name="negociationSetProduit", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociationSetProduit(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret']) || empty($data['prix']) || empty($data['codeNegociation'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }
        $prix
            = $data['prix'];
        $keySecret            = $data['keySecret'];
        $codeNegociation            = $data['codeNegociation'];
        $emetteur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $negociationProduit = $this->em->getRepository(NegociationProduit::class)->findOneBy(['codeNegociation' => $codeNegociation]);

        $negociationProduit->setPrixNegocie($prix);
        // ici on va generer le lien de paiement pour le produit  et specifiant au client
        // que le lien a un temps d'utilisation limite
        $message            = $data['message'];

        $messageProduit = new MessageNegociation();
        $boutiqueId
            = $negociationProduit->getProduit()->getBoutique()->getUser()->getId();
        $messageProduit->setEmetteur($emetteur->getId() == $boutiqueId);
        $messageProduit->setMessage($message);
        $messageProduit->setNegociation($negociationProduit);
        $this->em->persist(
            $messageProduit
        );
        $this->em->flush();

        $data = [
            'canal'
            =>  $this->myFunction->getCodeNegociation(),
            'data' => [
                'message' => $message,
                'emetteurId' =>  $emetteur->getId()
            ]
        ];



        $this->myFunction->Socekt_Emit("negociation", $data);
        return new JsonResponse([
            'status' => true,


        ], 200);
    }



    /**
     * @Route("/negociation/test", name="negociationMessageTest", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function negociationMessageTest(Request $request)
    {
        $message = $request->query->get('message');


        $data = [
            'canal'
            =>  "cn1RDIdfYE",
            'data' => [
                'message' =>
                $message,
                'emetteurId' => 2,


                'date' => (new \DateTime())->format('Y-m-d'),
                'heure' => (new \DateTime())->format('H:i'),
            ]
        ];



        $this->myFunction->Socekt_Emit("negociation", $data);;
        return new JsonResponse([
            'status' => true,


        ], 200);
    }
}
