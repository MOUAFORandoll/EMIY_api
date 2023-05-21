<?php

namespace App\Controller;

use App\Entity\Compte;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Symfony\Component\Serializer\SerializerInterface;
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
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\FunctionU\MyFunction;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Connexion;
use App\Entity\UserPlateform;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\Transaction;
use App\Entity\TypeTransaction;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;


class TransactionController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $user;
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
        RefreshTokenManagerInterface $jwtRefresh,
        ValidatorInterface
        $validator,
        MyFunction  $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->user = $user;
        $this->jwt = $jwt;
        $this->jwtRefresh = $jwtRefresh;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }




    /**
     * 
     * @Route("/transaction/read", name="transactionReadUser", methods={"POST"})
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
    public function transactionReadUser(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $data['id']]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }


        $ltransaction
            = $this->em->getRepository(Transaction::class)->findBy(['client' => $user]);

        $ftransaction = [];
        foreach ($ltransaction  as $transaction) {
            $ftransaction[] = [
                'id' => $transaction->getId(),
                'nom' => $transaction->getClient()->getNom(),
                // 'image' => 'image',
                'prenom' => $transaction->getClient()->getPrenom(),
                'montant' => $transaction->getMontant(),
                'status' => $transaction->isStatus() ? 'Valide' : 'Echoue',
                'typeTransaction' => $transaction->getTypeTransaction()->getLibelle(),
                'typeTransactionId' => $transaction->getTypeTransaction()->getId(),
                'dateCreated' => date_format($transaction->getDateCreate(), 'Y-m-d H:i'),
            ];
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $ftransaction
                ],
                200
            );
    }



    /**
     * @Route("/transaction/retrait", name="transacionRetraitUser", methods={"POST"})
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
    public function transacionRetraitUser(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;


        if (empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }
        if (empty($data['montant'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner un montant'

            ], 203);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if (!$user) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        $montant
            = $data['montant'];

        $data['montant'];

        $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);

        if (!$compte) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

            ], 203);
        }
        if (
            $compte->getSolde() >= $montant
        ) {

            $tyPeT
                = $this->em->getRepository(TypeTransaction::class)->findOneBy(['id' => 2]);
            if (!$tyPeT) {
                return new JsonResponse([
                    'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

                ], 203);
            }
            $modePaiement
                = $this->em->getRepository(ModePaiement::class)->findOneBy(['id' => 1]);

            if (!$modePaiement) {
                return new JsonResponse([
                    'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

                ], 203);
            }
            $transaction = [
                'libelle' => $tyPeT->getLibelle(),
                'montant' => $montant,

                'nom' => $user->getNom(),
                // 'image' => 'image',
                'prenom' => $user->getPrenom(),
                'numeroClient' => $data['phone'] ?? $user->getPhone(),
                'token' => $user->getPhone(),
                'status' => true,
                'typeTransaction' => $tyPeT,
                'client' =>   $user,
                'modePaiement' => $modePaiement
            ];
            $transactionE =  $this->myFunction->addTransaction($transaction);
            $compte->setSolde($compte->getSolde() - $montant);
            $this->em->persist(
                $compte
            );
            $this->em->flush();
            return
                new JsonResponse(
                    [

                        'message'
                        =>
                        $transactionE ? 'Reussi' : 'Echoue'
                    ],
                    200
                );
        } else {
            return
                new JsonResponse(
                    [

                        'message'
                        =>
                        'Solde insuffisant'
                    ],
                    203
                );
        }
    }



    /**
     * @Route("/compte/credit", name="compteCredit", methods={"POST"})
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
    public function compteCredit(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();



        if (empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner tous les champs'

            ], 203);
        }
        if (empty($data['montant'])) {
            return new JsonResponse([
                'message' => 'Veuillez renseigner un montant'

            ], 203);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if (!$user) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        $montant
            = $data['montant'];



        $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);

        if (!$compte) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

            ], 203);
        }

        $tyPeT
            = $this->em->getRepository(TypeTransaction::class)->findOneBy(['id' => 3]);
        if (!$tyPeT) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

            ], 203);
        }

        $modePaiement
            = $this->em->getRepository(ModePaiement::class)->findOneBy(['id' => $data['idModePaiement']]);


        if (!$modePaiement) {
            return new JsonResponse([
                'message' => 'Vous ne pouvez pas poursuivre l\'operation, veuillez joindre un gestionnaire'

            ], 203);
        }
        $transaction = [
            'libelle' => $tyPeT->getLibelle(),
            'montant' => $montant,
            "email" => "hari.randoll@gmail.com",

            'nom' => $user->getNom(),
            // 'image' => 'image',
            'prenom' => $user->getPrenom(),
            'numeroClient' => $data['phone'] ?? $user->getPhone(),

            'typeTransaction' => $tyPeT,
            'client' =>   $user,
            'modePaiement' => $modePaiement
        ];
        $transactionE =  $this->myFunction->addFreeCoin($transaction);

        return $transactionE;
    }


    /**
     * @Route("/compte/credit/verify", name="verifyCreditCompte", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function verifyCreditCompte(Request $request)
    {
        /**
         * request doit contenir  modePaiement, token,idListSmsAchete, quantite
         */
        $data = $request->toArray();

        if (empty($data['token'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer veuillez contacter le developpeur'
            ], 400);
        }
        $token = $data['token'];

        $transaction = $this->em->getRepository(Transaction::class)->findOneBy(['token' => $token]);



        if (

            $transaction
        ) {
            $statusTransaction =
                $this->myFunction->verifyBuy($transaction->getToken());
            $user =
                $transaction->getClient();
            if ($user) {
                if ($statusTransaction && !$transaction->isStatus()) {



                    $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' =>  $user]);
                    $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                    $transaction->setStatus(true);
                    $this->em->persist(
                        $transaction
                    );
                    $this->em->persist(
                        $compte
                    );

                    $this->em->flush();
                    return new JsonResponse([
                        'status'
                        => true,

                        'message' => 'Recharge Effectue'
                    ], 201);
                } else {
                    return new JsonResponse([
                        'status'
                        => false,
                        'message' => 'En attente de validation de votre part'
                    ], 203);
                }
            } else {


                return new JsonResponse(
                    [
                        'status'
                        => false,

                    ],
                    400
                );
            }
        } else {


            return new JsonResponse(
                [
                    'status'
                    => false,

                ],
                400
            );
        }
    }
}
