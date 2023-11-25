<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\point_livraison;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;

use App\Entity\Compte;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\FunctionU\MyFunction;

use App\Entity\Connexion;
use App\Entity\ListProduitPanier;
use App\Entity\UserPlateform;
use App\Entity\Localisation;
use App\Entity\TypeUser;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Faker\Factory;

class PointLivraisonController extends AbstractController
{


    private $em;
    private   $serializer;
    private $clientWeb;

    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction   $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }


    /**
     * @Route("/point_livraison/new", name="PointLivraisonNew", methods={"POST"})
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
    public function PointLivraisonNew(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            empty($data['keySecret']) ||
            empty($data['libelle']) ||
            empty($data['ville']) ||
            empty($data['quartier']) ||
            empty($data['longitude']) ||
            empty($data['latitude'])

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

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin->getTypeUser()->getId() == 1) {


            $point_livraison = new PointLivraison();
            $point_livraison->setLibelle($data['libelle'])
                ->setVille($data['ville'])

                ->setQuartier($data['quartier'])
                ->setLongitude($data['longitude'])
                ->setLatitude($data['latitude']);

            $this->em->persist($point_livraison);
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
                    'message' => 'Aucune authorisation'
                ], 203);
        }
    }


    /**
     * @Route("/point_livraison/status", name="point_livraisonStatus", methods={"POST"})
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
    public function point_livraisonReStatus(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();




        if (

            empty($data['keySecret']) || empty($data['point_livraison'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $id = $data['id'];
        $keySecret = $data['keySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if ($admin) {
            if ($admin->getTypeUser()->getId() == 1) {





                $pointLivraison = $this->em->getRepository(PointLivraison::class)->findOneBy(['id' => $data['point_livraison']]);
                $pointLivraison->setStatus(!$pointLivraison->isStatus());
                $this->em->persist($pointLivraison);
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
     * @Route("/point_livraison/read", name="point_livraisonRead", methods={"GET"})
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
    public function point_livraisonRead(Request $request)
    {

        $list_point_livraison = [];
        $point_livraisons = $this->em->getRepository(PointLivraison::class)->findBy(['status' => 1]);
        foreach ($point_livraisons  as $point_livraison) {

            $list_point_livraison[]
                = [
                    'id' => $point_livraison->getId(),
                    'libelle' => $point_livraison->getLibelle(),
                    'ville' => $point_livraison->getVille(),
                    'quartier' => $point_livraison->getQuartier(),
                    'longitude' => $point_livraison->getLongitude(),
                    'latitude' => $point_livraison->getLatitude(),
                    'image' =>
                    $this->myFunction::BACK_END_URL . '/images/point_livraison_object/' . $point_livraison->getImage()
                ];
        }





        return    new JsonResponse(
            [
                'data'
                =>
                $list_point_livraison,

            ],
            200
        );
    }
    /**
     * @Route("/point_livraison/read/all", name="point_livraisonReadAll", methods={"GET"})
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
    public function point_livraisonReadAll(Request $request)
    {


        $listPoint = $this->em->getRepository(PointLivraison::class)->findAll();
        if ($listPoint) {




            $datas =
                $this->serializer->serialize(array_reverse($listPoint), 'json');
            return
                new JsonResponse([
                    'data'
                    =>
                    JSON_DECODE($datas),

                ], 200);
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 200);
        }
    }

    /**
     * @Route("/point_livraison/set", name="setLivr", methods={"GET"})
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
    public function setLivr(Request $request)
    {


        $point_livraisons = $this->em->getRepository(ListProduitPanier::class)->findListCommandeBoutique(11);

        $this->em->flush();
        return
            new JsonResponse([
                'data'
                => $point_livraisons

            ], 200);
    }
}