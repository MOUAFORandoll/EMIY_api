<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\livreur;
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
use App\Entity\UserPlateform;
use App\Entity\Localisation;
use App\Entity\TypeUser;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;


class LivreurController extends AbstractController
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
     * @Route("/livreur/new", name="livreurNew", methods={"POST"})
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
    public function livreurNew(Request $request)
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

                $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);
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
     * @Route("/livreur/remove", name="livreurRemove", methods={"POST"})
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
    public function livreurRemove(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();




        if (
            empty($data['id']) ||
            empty($data['adminkeySecret'])

        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $id = $data['id'];
        $adminkeySecret = $data['adminkeySecret'];

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $adminkeySecret]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $id]);
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
     * @Route("/livreur/read/ville", name="livreurReadVille", methods={"POST"})
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
    public function livreurReadVille(Request $request)
    {
        $data = $request->toArray();

        $possible = false;



        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);

        $llivreur = $this->em->getRepository(UserPlateform::class)->findBy(['typeUser' => $typeUser]);

        if ($llivreur) {

            $lP = [];
            foreach ($llivreur  as $livreur) {

                $lU
                    = $this->myFunction->getUserLocalisation($livreur);
                $livreurU =  [
                    'id' => $livreur->getId(),
                    'nom' => $livreur->getNom(),
                    'prenom' => $livreur->getPrenom(),
                    'phone' => $livreur->getPhone(),
                    'distance'
                    =>  $this->myFunction->calculDistance($lU['longitude'], $lU['latitude'], $data['longitude'], $data['latitude']),


                ];
                array_push($lP, $livreurU);
            }
            $llivreurF
                = $this->serializer->serialize($lP, 'json');

            return
                new JsonResponse(
                    [
                        'data'
                        => JSON_DECODE($llivreurF)
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
     * @Route("/livreur/read", name="livreurReadAll", methods={"POST"})
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
    public function livreurReadAll(Request $request)
    {

        $typeCompte = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);
        $data       = $request->toArray();
        $possible   = false;
        if (empty($data['keySecret'])) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 400);
        }
        $userUser = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if ($userUser) {
            $list_users_final = [];

            /**
             * si il a le role admin
             */
            if ($userUser->getTypeUser()->getId() == 1) {
                $luser = $this->em->getRepository(UserPlateform::class)->findAll();

                foreach ($luser as $user) {
                    if ($user->getTypeUser()->getId() == 3) {
                        $localisation = $user->getLocalisations()[count($user->getLocalisations()) - 1];
                        $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);
                        if ($compte) {
                            $userU        = [
                                'id' => $user->getId(),
                                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                                'status' => $user->isStatus(),
                                'typeUser' => $user->getTypeUser()->getId(),
                                'dateCreated' => date_format($user->getDateCreated(), 'Y-m-d H:i'),
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
                        } else {
                            $newCompte = new Compte();

                            $newCompte->setUser($user);
                            $newCompte->setSolde(0);


                            $this->em->persist($newCompte);
                            $this->em->flush();    # code...
                        }
                    } else {
                    }
                }
                $datas =
                    $this->serializer->serialize(array_reverse($list_users_final), 'json');
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
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 200);
        }
    }
}
