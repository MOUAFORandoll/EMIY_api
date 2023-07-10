<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\UserPlateform;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Swift_SendmailTransport;
use Symfony\Component\Serializer\SerializerInterface;
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
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\FunctionU\MyFunction;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Connexion;
use App\Entity\Localisation;
use App\Entity\TypeUser;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;


class UserManageController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $user;
    private $passwordEncoder;
    private $jwt;
    private $jwtRefresh;
    private $validator;
    private $myFunction;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        HttpClientInterface $user,
        JWTTokenManagerInterface $jwt,

        ValidatorInterface
        $validator,
        MyFunction  $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->user = $user;
        $this->jwt = $jwt;

        $this->validator = $validator;
        $this->mailer = $mailer;
    }


    /**
     * @Route("/user/get", name="getUserX", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserX(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer '
            ], 203);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);


        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question a des contraintes',

            ], 203);
        }
        $localisation = $user->getLocalisations()[count($user->getLocalisations()) - 1];
        $userU = [
            'id' => $user->getId(),
            'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(), 'phone' => $user->getPhone(),
            'status' => $user->isStatus(),
            'typeUser' => $user->getTypeUser()->getId(),
            'dateCreated' => date_format($user->getDateCreated(), 'Y-m-d H:i'),
            'localisation' =>    $localisation  ? [
                'ville' =>
                $localisation->getVille(),

                'longitude' =>
                $localisation->getLongitude(),
                'latitude' =>
                $localisation->getLatitude(),
            ] : []
            // 'nom' => $user->getNom()
        ];
        $compteU =  $compte  ? [
            'id' => $compte->getId(),
            'solde' => $compte->getSolde() ?? 0,
        ] : [
            'id' => 0,
            'solde' =>  0,
        ];


        return new JsonResponse([
            // 'status' => 'ok',
            'data' =>  $userU,
            'compte' =>  $compteU,
        ], 200);
    }
    /**
     * @Route("/user/update", name="updateProfilClient", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfilClient(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer '
            ], 400);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question n\'existe pas dans la base de donnée'
            ], 400);
        }

        if (!empty($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (!empty($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }
        if (!empty($data['email'])) {
            $user->setEmail($data['email']);
        }


        if (!empty($data['phone'])) {
            $user->setPhone($data['phone']);
        }


        $this->em->persist($user);
        $this->em->flush();

        // $infoUser = $this->createNewJWT($user);
        // $tokenAndRefresh = json_decode($infoUser->getContent());

        // return new JsonResponse([
        //     'message' => 'success',
        //     'token' => $tokenAndRefresh->token,
        //     'refreshToken' => $tokenAndRefresh->refreshToken,
        // ], 200);
    }

    public function getNewPssw(/* $id */)
    {

        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }

        $chaine .= '@';
        for ($i = 0; $i < 2; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $id]);
        // $password = $this->passwordHasher->hashPassword(
        //     $user,
        //     $chaine
        // );
        // $user->setPassword($password);

        return $chaine;
    }
    /**
     * @Route("/update/password/user", name="updatePasswordClient", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePasswordClient(Request $request)
    {
        // $data = $request->toArray();
        // if (empty($data['userId'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayerle userId et password sont requis'
        //     ], 400);
        // }
        // $user = $this->em->getRepository(UserPlateform::class)->find((int)$data['userId']);

        // if (!$user) {
        //     return new JsonResponse([
        //         'message' => 'Desolez l\'utilisateur en question n\'existe pas dans la base de donnée'
        //     ], 400);
        // }
        // $npass = $data['password'] ??   $this->getNewPssw();

        // $password = $this->passwordEncoder->encodePassword(
        //     $user,
        //     $npass
        // );
        // $user->setPassword($password);
        // // $user->setFirstConnexion(false);
        // $this->em->persist($user);
        // $this->em->flush();

        // $infoUser = $this->createNewJWT($user);
        // $tokenAndRefresh = json_decode($infoUser->getContent());

        // return new JsonResponse([
        //     'password' => $npass,
        //     'token' => $tokenAndRefresh->token,
        //     'refreshToken' => $tokenAndRefresh->refreshToken,
        // ], 200);
    }
    // public function createNewJWT(UserPlateform $user)
    // {
    //     $token = $this->jwt->create($user);

    //     $datetime = new \DateTime();
    //     $datetime->modify('+2592000 seconds');

    //     $refreshToken = $this->jwtRefresh->create();

    //     $refreshToken->setUsername($user->getUsername());
    //     $refreshToken->setRefreshToken();
    //     $refreshToken->setValid($datetime);

    //     // Validate, that the new token is a unique refresh token
    //     $valid = false;
    //     while (false === $valid) {
    //         $valid = true;
    //         $errors = $this->validator->validate($refreshToken);
    //         if ($errors->count() > 0) {
    //             foreach ($errors as $error) {
    //                 if ('refreshToken' === $error->getPropertyPath()) {
    //                     $valid = false;
    //                     $refreshToken->setRefreshToken();
    //                 }
    //             }
    //         }
    //     }

    //     $this->jwtRefresh->save($refreshToken);

    //     return new JsonResponse([
    //         'token' => $token,
    //         'refreshToken' => $refreshToken->getRefreshToken()
    //     ], 200);
    // }

    /**
     * @Route("/desactivate/user", name="desactivateClient", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function desactivateProfilClient(Request $request)
    {
        $data = $request->toArray();
        // if (empty($data['userId']) || empty($data['status'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayerle userId et status sont requis'
        //     ], 400);
        // }
        $user = $this->em->getRepository(UserPlateform::class)->find((int)$data['userId']);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question n\'existe pas dans la base de donnée'
            ], 400);
        }


        $user->setStatus($data['status']);
        $this->em->persist($user);
        $this->em->flush();

        // $infoUser = $this->createNewJWT($user);
        // $tokenAndRefresh = json_decode($infoUser->getContent());

        // return new JsonResponse([
        //     'token' => $tokenAndRefresh->token,
        //     'refreshToken' => $tokenAndRefresh->refreshToken,
        // ], 200);
    }




    /**
     * @Route("/forgot/password", name="forgotPassword", methods={"POST"})
     * @param Request $request action = 1 => verifier exist numero ou email, action = 2 => send code phone, action = 3 => send code email , action = 4 => verify code
     * @return JsonResponse
     * 
     */
    public function forgotPassword(Request $request)
    {
        // $data = $request->toArray();
        // if (empty($data['action'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayeraction est requis'
        //     ], 400);
        // }

        // $action = $data['action'];
        // if ($action == 1) {
        //     if (!empty($data['phone']) || !empty($data['email'])) {
        //         $user =
        //             !empty($data['phone']) ? $this->em->getRepository(UserPlateform::class)->findOneBy([
        //                 'phone' => $data['phone']
        //             ]) : $this->em->getRepository(UserPlateform::class)->findOneBy([
        //                 'email' => $data['email']
        //             ]);
        //         if ($user) {
        //             return new JsonResponse([
        //                 'message' => 'Utilisateur correct, veuillez poursuivre',
        //                 'user' => $user->getKeySecret(),
        //                 'status' => true
        //             ], 200);
        //         } else {
        //             return new JsonResponse([
        //                 'message' => 'Utilisateur inexistant',

        //                 'status' => false
        //             ], 203);
        //         }
        //     } else {
        //         return new JsonResponse([
        //             'message' => 'Renseigner un numero ou une adresse email',

        //             'status' => false
        //         ], 400);
        //     }
        // }
        // if ($action == 2) {
        //     if (!empty($data['user'])) {
        //         $user =
        //             $this->em->getRepository(UserPlateform::class)->findOneBy([
        //                 'keySecret' => $data['user']
        //             ]);
        //         if ($user) {
        //             $code = $this->createCode();
        //             $user->setCodeRecup($code);
        //             $this->em->persist($user);
        //             $this->em->flush();
        //             $sendSms =   $this->sendCode($user->getPhone(), $user->getEmail(),   $code);
        //             if ($sendSms) {
        //                 return new JsonResponse([
        //                     'message' => 'Le code a ete transmis veuillez consulter votre appareil',

        //                     'status' => true
        //                 ], 200);
        //             } else {
        //                 return new JsonResponse([
        //                     'message' => 'Une erreur est survenue',

        //                     'status' => false
        //                 ], 203);
        //             }
        //         } else {
        //             return new JsonResponse([
        //                 'message' => 'Utilisateur inexistant',

        //                 'status' => false
        //             ], 203);
        //         }
        //     } else {
        //         return new JsonResponse([
        //             'message' => 'Renseigner un numero ou une adresse email',

        //             'status' => false
        //         ], 400);
        //     }
        // }
        // if ($action == 3) {
        //     if (!empty($data['user'])) {
        //         $user =
        //             $this->em->getRepository(UserPlateform::class)->findOneBy([
        //                 'keySecret' => $data['user']
        //             ]);
        //         if ($user) {
        //             $code = $this->createCode();
        //             $user->setCodeRecup($code);
        //             $this->em->persist($user);
        //             $this->em->flush();
        //             $sendSms =
        //                 $this->sendCode(null, $user->getEmail(),   $code);
        //             if ($sendSms) {
        //                 return new JsonResponse([
        //                     'message' => 'Le code a ete transmis veuillez consulter votre appareil',

        //                     'status' => true
        //                 ], 200);
        //             } else {
        //                 return new JsonResponse([
        //                     'message' => 'Une erreur est survenue',

        //                     'status' => false
        //                 ], 400);
        //             }
        //         } else {
        //             return new JsonResponse([
        //                 'message' => 'Utilisateur inexistant',

        //                 'status' => false
        //             ], 400);
        //         }
        //     } else {
        //         return new JsonResponse([
        //             'message' => 'Renseigner un numero ou une adresse email',

        //             'status' => false
        //         ], 400);
        //     }
        // }
        // if ($action == 4) {
        //     if (!empty($data['user']) && !empty($data['code'])) {
        //         $user =
        //             $this->em->getRepository(UserPlateform::class)->findOneBy([
        //                 'keySecret' => $data['user']
        //             ]);
        //         if ($user) {


        //             if (
        //                 $data['code']
        //                 == $user->getCodeRecup()
        //             ) {
        //                 return new JsonResponse([
        //                     'message' => 'Le code transmis est correct',
        //                     'user' => $user->getKeySecret(),
        //                     'status' => true
        //                 ], 200);
        //             } else {
        //                 return new JsonResponse([
        //                     'message' => 'Le code transmis est correct',

        //                     'status' => false
        //                 ], 203);
        //             }
        //         } else {
        //             return new JsonResponse([
        //                 'message' => 'Utilisateur inexistant',

        //                 'status' => false
        //             ], 203);
        //         }
        //     } else {
        //         return new JsonResponse([
        //             'message' => 'Renseigner un numero ou une adresse email',

        //             'status' => false
        //         ], 203);
        //     }
        // }
        // if ($action == 5) {

        //     if (empty($data['user']) || empty($data['password'])) {
        //         return new JsonResponse([
        //             'message' => 'Veuillez recharger la page et reessayerle user et password sont requis'
        //         ], 400);
        //     }
        //     $user =
        //         $this->em->getRepository(UserPlateform::class)->findOneBy([
        //             'keySecret' => $data['user']
        //         ]);
        //     if (!$user) {
        //         return new JsonResponse([
        //             'message' => 'Desolez l\'utilisateur en question n\'existe pas dans notre base de donnée'
        //         ], 203);
        //     }
        //     // $npass =   $this->serializerNewPssw();
        //     $user->setPassword($data['password']);
        //     $password = $this->passwordEncoder->encodePassword(
        //         $user,
        //         $user->getPassword()
        //     );
        //     $user->setPassword($password);
        //     $this->em->persist($user);
        //     $this->em->flush();

        //     $infoUser = $this->createNewJWT($user);
        //     $tokenAndRefresh = json_decode($infoUser->getContent());

        //     return new JsonResponse([
        //         'status' => true,
        //         'message' => 'Mot de passe mis a jour avec succes',
        //         'token' => $tokenAndRefresh->token,
        //         'refreshToken' => $tokenAndRefresh->refreshToken,
        //     ], 200);
        // }
    }

    public function sendCode($phone, $email,   $code)
    {

        // $routeManager =$this->em ->getManager('Route');
        // $message = 'Votre code de reinitilisation est : ' . $code;
        // if ($phone != null) {


        //     $user =
        //         $this->em->getRepository(UserPlateform::class)->findOneBy(['phone' => $phone]);
        //     // $contact  =   $this->createContact($user);
        //     $data = [
        //         'idClient'
        //         =>
        //         $user->getId(),
        //         // 'route' => $route,
        //         // 'contactId' =>
        //         // $contact['id'],
        //         'message' => $message,
        //         'senderId' =>

        //         'FahKap',


        //     ];
        //     $re =   $this->sendSmsApi($data);
        //     $this->myFunction->sendEmail(
        //         $email,
        //         $message,
        //         'Code de reinitialisation'
        //     );
        //     if ($re) {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // } else if ($email != null) {
        //     $this->myFunction->sendEmail(
        //         $email,
        //         $message,
        //         'Code de reinitialisation'
        //     );
        //     return true;
        // } else {
        //     return false;
        // }
    }


    function getPhone()
    {

        $phone = 6;
        $listeCar = '0123456789';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 8; ++$i) {
            $phone .= $listeCar[random_int(0, $max)];
        }
        $user =
            $this->em->getRepository(UserPlateform::class)->findOneBy([
                'phone' => $phone
            ]);
        if (!$user) {
            return $phone;
        } else {
            return   $this->getPhone();
        }
    }

    function getPassword()
    {

        $phone = '';
        $listeCar = '0123456789';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 4; ++$i) {
            $phone .= $listeCar[random_int(0, $max)];
        }

        return $phone;
    }

    public function createCode()
    {

        $code = '';
        $listeCar = '0123456789';

        for ($i = 0; $i < 4; ++$i) {
            $code .= $listeCar[random_int(0, 9)];
        }
        $ExistTransaction = $this->em->getRepository(UserPlateform::class)->findOneBy(['codeRecup' => $code]);
        if ($ExistTransaction) {
            return
                $this->createCode();
        } else {
            return      $code;
        }
    }

    public function sendSmsApi($data)
    {
        //voir devoo projet
    }

    /**
     * @Route("/user/location", name="userSendLocation", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function userSendLocation(Request $request)
    {
        $data = $request->toArray();

        if (empty($data['keySecret']) || empty($data['ville']) || empty($data['ville']) || empty($data['latitude']) || empty($data['ip'])) {
            return new JsonResponse([
                'status' => false
            ], 203);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$user) {
            return new JsonResponse([
                'status' => false
            ], 203);
        }

        $localisation = new Localisation();
        $localisation->setUser($user);
        $localisation->setVille($data['ville']);
        $localisation->setLongitude($data['longitude']);
        $localisation->setLatitude($data['latitude']);
        $localisation->setIp($data['ip']);
        $this->em->persist($localisation);
        $this->em->flush();

        return new JsonResponse([
            'status' => true,
        ], 200);
    }
   


    /**
     * @Route("/admin/read", name="adminReadAll", methods={"POST"})
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
    public function adminReadAll(Request $request)
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
                    if ($user->getTypeUser()->getId() == 1) {
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
                $datas
                    = $this->serializer->serialize(array_reverse($list_users_final), 'json');
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
    /**
     * @Route("/user/fieul", name="UserFieul", methods={"POST"})
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
    public function UserFieul(Request $request)
    {

        $data       = $request->toArray();

        if (empty($data['keySecret'])) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 400);
        }
        $userUser = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if ($userUser) {




            $luser = $this->em->getRepository(UserPlateform::class)->findBy(['codeParrain' => $userUser->getId()]);

            $datas
                = $this->serializer->serialize(array_reverse($luser), 'json');
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
                'message' => 'Aucun'
            ], 200);
        }
    }
}
