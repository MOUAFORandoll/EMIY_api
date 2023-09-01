<?php

namespace App\FunctionU;

use App\Entity\AbonnementBoutique;
use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Commande;
use App\Entity\Commission;
use App\Entity\Communication;
use App\Entity\Compte;
use App\Entity\HistoriquePaiement;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
use App\Entity\NegociationProduit;
use App\Entity\NotationBoutique;
use App\Entity\LikeProduit;
use App\Entity\Notification;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Short;
use App\Entity\ShortComment;
use App\Entity\ShortCommentLike;
use App\Entity\ShortLike;
use App\Entity\Transaction;
use App\Entity\TypeNotification;
use App\Entity\UserPlateform;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\File\File as FileFile;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class MyFunction
{
    public $emetteur = 'admin@prikado.com';
    public $host_serveur_socket;

    private $em;
    private $client;
    private
        $token    = "sb.sX32rcaw0TdQopyWxXA0DwJTCOG0o2EA";

    const
        BACK_END_URL =
        'http://172.20.10.10:8000';
    const
        PAGINATION = 14;
    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $client,


    ) {

        $this->host_serveur_socket
            =/*  $_SERVER['REQUEST_SCHEME'] . ://.  $_SERVER['SERVER_ADDR'] */ 'http://127.0.0.1' . ':3000';
        $this->client =
            $client;
        $this->em = $em;
    }
    public function removeSpace(string $value)
    {
        return str_replace(' ', '', rtrim(trim($value)));
    }

    public function getUniqueNameProduit()
    {


        $chaine = 'produit';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(ProduitObject::class)->findOneBy(['src' => $chaine . 'jpg']);
        $ExistCode1 = $this->em->getRepository(ProduitObject::class)->findOneBy(['src' => $chaine . 'png']);
        if ($ExistCode ||  $ExistCode1) {
            return
                $this->getUniqueNameProduit();
        } else {
            return $chaine;
        }
    }

    public function getUniqueNameBoutiqueImg()
    {


        $chaine = 'bt';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(BoutiqueObject::class)->findOneBy(['src' => $chaine . 'jpg']);
        $ExistCode1 = $this->em->getRepository(BoutiqueObject::class)->findOneBy(['src' => $chaine . 'png']);
        if ($ExistCode ||  $ExistCode1) {
            return
                $this->getUniqueNameBoutiqueImg();
        } else {
            return $chaine;
        }
    }
    public function getUserLocalisation($user)
    {
        $locations = $this->em->getRepository(Localisation::class)->findBy(['user' => $user]);

        if ($locations) {

            $location =
                $locations[count($locations) - 1];
            return [
                'ville'
                => $location->getVille(),
                'longitude'
                => $location->getLongitude(),
                'latitude'
                => $location->getLatitude()

            ];
        } else {
            return [
                'ville'
                => '',
                'longitude'
                => 0,
                'latitude'
                => 0
            ];
        }
    }
    public function calculDistance($longU, $latU, $longL, $latL)
    {

        // convert from degrees to radians
        $latFrom = deg2rad($longU);
        $lonFrom = deg2rad($latU);
        $latTo = deg2rad($longL);
        $lonTo = deg2rad($latL);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return ceil($angle * 6371000 / 1000);
    }


    public function buyCommissionProduitBoutique(int $produitpanier, int $idCommande)
    {
        $listPP = $this->em->getRepository(ListProduitPanier::class)->findOneBy(['id' => $produitpanier]);
        $commission = $this->em->getRepository(Commission::class)->findOneBy(['id' => 1]);
        $typeBoutique = $this->em->getRepository(TypePaiement::class)->findOneBy(['id' => 2]);
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $idCommande]);

        $qte = $listPP->getQuantite();

        $produit =
            $listPP->getProduit();
        $price = ($produit->getPrixUnitaire() -
            $produit->getPrixUnitaire() * $commission->getPourcentageProduit() / 100
            - $commission->getFraisLivraisonProduit()


        ) *    $qte;

        if ($produit) {
            $boutique =
                $produit->getBoutique();
            $user = $boutique->getUser();
            if ($boutique && $user) {
                $data = [
                    'commande' => $commande,
                    'montant' => $price,
                    'typePaiement' => $typeBoutique,
                    'user' => $user,
                    'token' => '2222'
                ];

                $addp =  $this->addPaiement($data);
                if ($addp != null) {
                    $this->buyBoutiqueAccount($boutique->getId(), $price);
                }
            }
        }
    }



    public function buyCommissionLivreur(int $idCommande)
    {
        $typeLivreur = $this->em->getRepository(TypePaiement::class)->findOneBy(['id' => 1]);

        $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $idCommande]);
        $commission = $this->em->getRepository(Commission::class)->findOneBy(['id' => 1]);
        if ($commande) {


            if ($commande->getListCommandeLivreurs()) {
                $livreur = $commande->getListCommandeLivreurs()->last()->getLivreur();
                if ($livreur) {
                    $data = [
                        'commande' => $commande,
                        'montant' => $commission->getFraisBuyLivreur(),
                        'typePaiement' => $typeLivreur,
                        'user' => $livreur,
                        'token' => '2222'
                    ];

                    $addp =  $this->addPaiement($data);
                    if ($addp != null) {
                        $this->buyLivreurAccount($livreur->getId(), $commission->getFraisBuyLivreur());
                    }
                }
            }
        }
    }





    public function buyBoutiqueAccount(int $idBoutique, int $montant)
    {

        //gerer les historique de transaction paiement et le type de paiement livreur boutique ou systeme 
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['id' => $idBoutique]);
        if ($boutique) {
            if ($boutique->getUser()) {

                $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $boutique->getUser()]);
                $compte->setSolde($compte->getSolde() + $montant);
                $this->em->persist(
                    $compte
                );
                $this->em->flush();
                return 0;
            } else {
                //signalement admin
            }
        } else {
            //signalement admin
        }
    }

    public function buyLivreurAccount(int $idLivreur, int $montant)
    {
        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $idLivreur]);
        if ($livreur) {

            $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $livreur]);
            $compte->setSolde($compte->getSolde() + $montant);
            $this->em->persist(
                $compte
            );
            $this->em->flush();
            return 0;
        } else {
            //signalement admin
        }
        return 0;
    }


    public function addTransaction($data)
    {
        try {
            $transaction = new Transaction();
            $transaction->setLibelle($data['libelle']);
            $transaction->setMontant($data['montant']);
            $transaction->setNomClient($data['nom'] ?? '');
            $transaction->setPrenomClient($data['prenom'] ?? '');
            $transaction->setNumeroClient($data['numeroClient'] ?? '');
            $transaction->setToken($data['token']);
            $transaction->setStatus($data['status']);
            $transaction->setClient($data['client'] ?? null);
            $transaction->setTypeTransaction($data['typeTransaction']);
            if (!empty($data['panier'])) {
                $transaction->setPanier($data['panier']);
            }
            $transaction->setModePaiement($data['modePaiement']);
            $this->em->persist(
                $transaction
            );
            $this->em->flush();
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function addFreeCoin($data)
    {
        try {
            $transaction = new Transaction();
            $transaction->setLibelle($data['libelle']);
            $transaction->setMontant($data['montant']);
            $transaction->setNomClient($data['nom'] ?? '');
            $transaction->setPrenomClient($data['prenom'] ?? '');
            $transaction->setNumeroClient($data['numeroClient'] ?? '');

            $transaction->setStatus(false);
            $transaction->setClient($data['client']);
            $transaction->setTypeTransaction($data['typeTransaction']);

            $transaction->setModePaiement($data['modePaiement']);

            $dataRequest
                =   [
                    "email" => "hari.randoll@gmail.com",
                    "phone" => $data['numeroClient'],
                    "name" => $data['nom'],
                    "amount" => $data['montant'],
                    "currency" => "XAF",
                    "reference" => $this->reference(),
                    "description" => "Initialisation paiement pourrecharge compte",

                ];
            $response = $this->client->request(
                'POST',
                'https://api.notchpay.co/payments/initialize',
                [

                    'headers' => ['Accept' => 'application/json', 'Authorization' => $this->token],
                    "json" => $dataRequest
                ]
            );

            $statusCodeInit = $response->getStatusCode();
            if ($statusCodeInit == 201) {
                if ($response->toArray()['code'] == 201) {

                    $transaction->setToken($response->toArray()["transaction"]['reference']);

                    $this->em->persist(
                        $transaction
                    );
                    $this->em->flush();

                    return new JsonResponse(
                        [
                            'token' => $response->toArray()["transaction"]['reference'],
                            'message' => 'Confirmer votre paiement',

                            'url' =>  $response->toArray()['authorization_url'],


                        ],
                        201
                    );
                } else {

                    return new JsonResponse([
                        'message' => 'Une erreur est survenue, reessayer',
                        'status' => false,

                    ], 203);
                }
            } else {
                return new JsonResponse([
                    'message' => 'Une erreur est survenue, reessayer ',
                    'data' => $response->toArray(),
                    'status' => false,

                ], 203);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function paid($data,  $montant, $idCom)
    {
        $reponse = [];
        /**
         * request doit contenir 
         * si sourcePaiement 1 => compteLocal: [clientId,recepteurId,idLicence,routeId,quantite, montant Total de la transaction];
         *  si sourcePaiement 2 => electronique paiement :[ address, city, countryCode,  codeZip, type
         * quantiteSms , routeId, clientId,recepteurId,idLicence,  montant Total de la transaction]
         */

        if ($data['idModePaiement'] == '1' || $data['idModePaiement'] == '2' || $data['idModePaiement'] == 1 || $data['idModePaiement'] == 2) {
            $dataRequest
                =   [
                    "email" => "hari.randoll@gmail.com",
                    "phone" => $data['phone'],
                    "name" => $data['nom'] . ' ' .  $data['nom'],
                    "amount" => $montant,
                    "currency" => "XAF",
                    "reference" => $this->reference(),
                    "description" => "Initialisation paiement entrant",
                    "idCom" => $idCom
                ];
            $paymob =     $this->mobileBuy($dataRequest);
            return $paymob;
        } else 
        if ($data['idModePaiement'] == '3') {

            try {
                Stripe::setApiKey('sk_test_51MprWdFGCxqI1QzHZR3w2uP5G7oLhl58hXt4MDHqCUjywE1bdCP5YC4aqr0VVHilCTYmY7qohQfH4SyzvMD6bqKP00mxclsFcy');

                $token = Token::create([
                    'card' => [
                        'number' => '4242424242424242',
                        'exp_month' => 03,
                        'exp_year' => 2026,
                        'cvc' => '868',
                    ],
                ]);
                $charge = Charge::create([
                    'amount' => 1000,
                    // montant en centimes
                    'currency' => 'eur',
                    'source' => $token,
                    // token de carte de crédit généré par Stripe.js
                    'description' => 'Achat de produits',
                ]);

                if ($charge['status'] = 'success') {
                    $reponse = true;
                }
                return $reponse;
            } catch (Exception $e) {
                $reponse = 0;
            }
        } else  if ($data['idModePaiement'] == '4' || $data['idModePaiement'] == 4) {
            $dataRequest
                =   [
                    "email" => "hari.randoll@gmail.com",
                    "phone" => $data['phone'],
                    "name" => $data['nom'] . ' ' .  $data['nom'],
                    "amount" => $montant,
                    "currency" => "XAF",
                    "reference" => $this->reference(),
                    "description" => "Initialisation paiement entrant",
                    "idCom" => $idCom
                ];
            $paymob =     $this->systemBuy($dataRequest);
            return $paymob;
        }
    }


    public function addPaiement($data)
    {
        $paiement = new HistoriquePaiement();
        $paiement->setCommande($data['commande']);
        $paiement->setMontant($data['montant']);
        $paiement->setTypePaiement($data['typePaiement']);
        $paiement->setUser($data['user']);

        $paiement->setToken($data['token']);
        $this->em->persist($paiement);
        $this->em->flush();
        return   $paiement->getId();
    }
    public function isLike_Produit($id)
    {
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);
        $notes = $this->em->getRepository(LikeProduit::class)->findBy(['produit' => $produit, 'like_produit' => true]);
        $noteL   = 0;
        // foreach ($notes as   $note) {
        //     $noteL += $note->isLike_produit();


        //     # code...
        // }
        return

            count($notes) /* != 0 ?   count($notes) : 0.0 */;
    }
    public function userlikeProduit(int $id, UserPlateform $user)
    {
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);
        $notes = $this->em->getRepository(LikeProduit::class)->findBy(['produit' => $produit, 'like_produit' => true, 'client' => $user]);

        return ($notes != null) ? true : false;
    }
    public function userlikeShort($short, UserPlateform $user)
    {
        $notes = $this->em->getRepository(ShortLike::class)->findBy(['short' => $short, 'like_short' => 1, 'client' => $user]);

        return ($notes != null) ? true : false;
    }
    public function userlikeShortCom(ShortComment $shortComment, UserPlateform $user)
    {
        $notes = $this->em->getRepository(ShortCommentLike::class)->findBy(['shortComment' => $shortComment, 'like_comment' => 1, 'client' => $user]);

        return ($notes != null) ? true : false;
    }
    public function userabonnementBoutique($boutique,   $user)
    {
        if (!$user) {
            return
                false;
        }
        if (!$boutique) {
            return
                false;
        }

        $abonnementExist = $this->em->getRepository(AbonnementBoutique::class)->findOneBy(['boutique' => $boutique, 'client' => $user]);


        return ($abonnementExist != null) ? true : false;
    }


    public function noteBoutique($id)
    {
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['id' => $id]);
        $notes = $this->em->getRepository(NotationBoutique::class)->findBy(['boutique' => $boutique]);
        $noteL   = 0;
        foreach ($notes as   $note) {
            $noteL += $note->isLike_produit();


            # code...
        }
        return

            count($notes) != 0 ?   $noteL / count($notes) : 0.0;
    }


    public function systemBuy($data)
    {
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $data['idCom']]);
        $user =
            $commande->getPanier()->getUser();
        if ($user) {
            $compte = $user->getComptes()[0];
            if ($compte->getSolde() >= $data['amount']) {
                $compteU = $this->em->getRepository(Compte::class)->findOneBy(['id' =>  $compte->getId()]);

                $compteU->setSolde(
                    $compteU->getSolde() - $data['amount']
                );

                $commande->setToken('System');
                $commande->setStatusBuy(true);
                $this->em->persist($commande);
                $this->em->persist($compteU);







                $this->em->flush();

                return new JsonResponse(
                    [
                        'message' => 'Achat effectue',
                        'status' => true,
                        'finish' => true,
                        'id' =>  $commande->getId(),
                        'codeClient'  => $commande->getCodeClient(),
                        'codeCommande' =>  $commande->getCodeCommande(),
                        'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i')

                    ],
                    201
                );
            } else {
                return new JsonResponse([
                    'message' => 'Solde inssufisant,recharger puis reessayer',
                    'status' => false,

                ], 203);
            };
        } else {
            return new JsonResponse([
                'message' => 'Vous devez avoir un compte pour effectuer cette action',
                'status' => false,

            ], 203);
        }
    }

    public function mobileBuy($data)
    {

        $response = $this->client->request(
            'POST',
            'https://api.notchpay.co/payments/initialize',
            [

                'headers' => ['Accept' => 'application/json', 'Authorization' => $this->token],
                "json" => $data
            ]
        );

        $statusCodeInit = $response->getStatusCode();
        if ($statusCodeInit == 201) {
            if ($response->toArray()['code'] == 201) {
                $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $data['idCom']]);

                $commande->setToken($response->toArray()["transaction"]['reference']);
                $this->em->persist($commande);

                $this->em->flush();
                return new JsonResponse(
                    [
                        'message' => 'Confirmer votre paiement',

                        'url' =>  $response->toArray()['authorization_url'],
                        'status' => true,
                        'finish' => false,
                        // 'a' =>  $panier->getListProduits(),
                        'id' =>  $commande->getId(),
                        'codeClient'  => $commande->getCodeClient(),
                        'codeCommande' =>  $commande->getCodeCommande(),
                        'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i')

                    ],
                    201
                );
            } else {

                return new JsonResponse([
                    'message' => 'Une erreur est survenue, reessayer',
                    'status' => false,

                ], 203);
            }
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue, reessayer',

                'status' => false,

            ], 203);
        }
    }


    public function verifyBuy($reference)
    {

        $response = $this->client->request(
            'GET',
            'https://api.notchpay.co/payments/' . $reference . '?currency=xaf',
            [

                'headers' => ['Content-Type' => 'application/json', 'Authorization' => $this->token],

            ]
        );
        $statusCodeInit = $response->getStatusCode();
        if ($statusCodeInit == 200) {
            if ($response->toArray()['code'] == 200) {
                return
                    $response->toArray()["transaction"]['status'] == 'complete';
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function reference()
    {

        $chaine = 'produit';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 31; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        return $chaine;
    }

    //commande
    //transaction
    //negociation

    public function getUniqueCodeCommunication()
    {


        $chaine = 'communication';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(Communication::class)->findOneBy(['codeCommunication' => $chaine]);
        if ($ExistCode) {
            return
                $this->getUniqueCodeCommunication();
        } else {
            return $chaine;
        }
    }

    public function Socekt_Emit($canal, $data)
    {



        $first =   $this->client->request('GET',   $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        $content = $first->getContent();
        $index = strpos($content, 0);
        $res = json_decode(substr($content, $index + 1), true);
        $sid = $res['sid'];
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => '40'
        ]);
        // $this->client->request('GET',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}");
        // $dataSign = ['signin', '350'];
        $dataEmit = [$canal, json_encode($data)];

        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42["%s", %s]', $userID, json_encode($dataEmit))
        // ]);
        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s',  json_encode($dataSign))
        // ]);
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => sprintf('42%s',  json_encode($dataEmit))
        ]);
    }

    public function Socekt_Emit_general($data)
    {



        $first =   $this->client->request('GET',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        $content = $first->getContent();
        $index = strpos($content, 0);
        $res = json_decode(substr($content, $index + 1), true);
        $sid = $res['sid'];
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => '40'
        ]);
        // $this->client->request('GET',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}");
        // $dataSign = ['signin', '350'];
        $dataEmit = ['general', json_encode($data)];

        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42["%s", %s]', $userID, json_encode($dataEmit))
        // ]);
        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s',  json_encode($dataSign))
        // ]);
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => sprintf('42%s',  json_encode($dataEmit))
        ]);
    }

    public function getCodeNegociation()
    {


        $code = 'cn';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 8; ++$i) {
            $code .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(NegociationProduit::class)->findOneBy(['codeNegociation' => $code]);
        if ($ExistCode) {
            return
                $this->getUniqueNameProduit();
        } else {
            return $code;
        }
    }
    /**
     * Summary of getUniqueNameShort
     * @return string
     */
    public function getUniqueNameShort()
    {


        $code = 'csn';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 18; ++$i) {
            $code .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(Short::class)->findOneBy(['codeShort' => $code]);
        if ($ExistCode) {
            return
                $this->getUniqueNameProduit();
        } else {
            return $code;
        }
    }






    function   createNotification($idtype, $data)
    {


        $type =
            $this->em->getRepository(TypeNotification::class)->findOneBy(['id' => $idtype]);


        $notification = new Notification();
        $notification->setTitle($data['title'] ?? '');
        $notification->setDescription($data['description'] ?? '');
        $notification->setInitiateur($data['user']);
        $notification->setTypeNotification($type);
        switch ($type->getId()) {

                // notificaton general
            case 1:
                $this->em->persist($notification);
                $this->em->flush();
                return
                    $notification;

                // notificaton like short
            case 2:
                $notification->setShortLike($data['sujet']);
                $notification->setRecepteur($data['sujet']->getShort()->getBoutique()->getUser());
                $this->em->persist($notification);
                $this->em->flush();
                return  $notification;

                // notificaton commentaire short
            case 3:
                $notification->setShortCommentaire($data['sujet']);
                $data['sujet']->getReferenceCommentaire() == null ?
                    $notification->setRecepteur($data['sujet']->getShort()->getBoutique()->getUser())
                    :
                    $notification->setRecepteur($data['sujet']->getReferenceCommentaire()->getClient());

                $this->em->persist($notification);
                $this->em->flush();
                return  $notification;

                // notificaton like  commentaire short

            case 4:
                $notification->setShortCommentLike($data['sujet']);

                $data['sujet']->getShortComment()->getReferenceCommentaire() == null ?
                    $notification->setRecepteur($data['sujet']->getShortComment()->getClient())
                    :
                    $notification->setRecepteur($data['sujet']->getShortComment()->getReferenceCommentaire()->getClient());
                $this->em->persist($notification);
                $this->em->flush();
                return  $notification;
                // notificaton mesage de negociation

            case 5:
                $notification->setMessageNegociation($data['sujet']);
                $notification->setRecepteur($data['sujet']->getNegociation()->getInitiateur());
                $this->em->persist($notification);
                $this->em->flush();
                return  $notification;

                // notificaton mesage de communication avec service cleint
            case 6:
                $notification->setMessageCommunication($data['sujet']);
                $notification->setRecepteur($data['sujet']->getCommunication()->getClient());
                $this->em->persist($notification);
                $this->em->flush();
                return  $notification;


            default:
                # code...
                break;
        }
    }

    function
    modelNotification(Notification $notification)
    {


        $profile      = count($notification->getInitiateur()->getUserObjects())  == 0 ? '' : $notification->getInitiateur()->getUserObjects()->first()->getSrc();



        $type =  $notification->getTypeNotification()->getId();

        switch ($type) {
                //data de  general
            case 1:
                return [
                    'id' => $notification->getId(),
                    'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                    'read'  => $notification->isRead(),
                    'title' => $notification->getTitle(),
                    'description' => $notification->getDescription(),
                    'type_notification' => 1,
                    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,

                ];
                //data de  like short
            case 2:
                return
                    $notification->getInitiateur()->getId() != $notification->getShortLike()->getShort()->getBoutique()->getUser()->getId() ?
                    [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getShortLike()->getClient()->getNom(),
                        'description' => 'A like votre short',
                        'type_notification' => 2,
                        'profile' => $this::BACK_END_URL . '/images/users/' . $profile,
                        'short' => $notification->getShortLike()->getShort()->getId(),
                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        // 'recepteur' => $notification->getShortLike()->getShort()->getBoutique()->getUser()->getKeySecret()

                    ] : null;

                //data de  commantaire short

            case 3:
                return
                    $notification->getShortCommentaire()->getReferenceCommentaire() == null  ?   [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getShortCommentaire()->getClient()->getNom(),
                        'description' =>  'A Commente votre short',
                        'type_notification' => 3,    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,
                        'short' => $notification->getShortCommentaire()->getShort()->getId(),

                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        // 'recepteur' =>  $notification->getShortCommentaire()->getClient()->getId() != $notification->getShortCommentaire()->getShort()->getBoutique()->getUser()->getId() ?  $notification->getShortCommentaire()->getShort()->getBoutique()->getUser()->getKeySecret() : 0

                    ] :   [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getShortCommentaire()->getClient()->getNom(),
                        'description' =>  'A repondu a votre commentaire',
                        'type_notification' => 3,    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,


                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        //   'recepteur' =>   $notification->getShortCommentaire()->getReferenceCommentaire()->getClient()->getKeySecret()

                    ];
                //data de like commantaire short
            case 4:
                return
                    $notification->getShortCommentLike()->getShortComment()->getReferenceCommentaire() == null ?   [


                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getShortCommentLike()->getClient()->getNom(),
                        'description' => 'A like votre commentaire',
                        'type_notification' => 4,
                        'profile' => $this::BACK_END_URL . '/images/users/' . $profile,

                        'short' => $notification->getShortCommentLike()->getShortComment()->getShort()->getId(),

                        'recepteur' =>  $notification->getShortCommentLike()->getClient()->getId() !=  $notification->getShortCommentLike()->getShortComment()->getClient()->getId() ? $notification->getShortCommentLike()->getShortComment()->getClient()->getKeySecret() : 0

                    ] : [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getShortCommentLike()->getClient()->getNom(),
                        'description' => 'A like votre commentaire',
                        'type_notification' => 4,    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,
                        'short' => $this->getIdShort($notification->getShortCommentLike()->getShortComment()),

                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        // 'recepteur' =>  $notification->getShortCommentLike()->getShortComment()->getReferenceCommentaire()->getClient()->getKeySecret()

                    ];
                // data de message negociation
            case 5:
                return ($notification->getMessageNegociation()->getInitiateur()->getId() != $notification->getMessageNegociation()->getNegociation()->getInitiateur()->getId()) ?
                    [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read' => $notification->isRead(),
                        'title' => $notification->getMessageNegociation()->getInitiateur()->getNom(),
                        'description' => $notification->getMessageNegociation()->getMessage(),
                        'type_notification' => 5,    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,


                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        // 'recepteur' =>  $notification->getMessageNegociation()->getNegociation()->getInitiateur()->getKeySecret(),

                    ] : null;
                // data de message communication

            case 6:
                return ($notification->getMessageCommunication()->getInitiateur()->getId() != $notification->getMessageCommunication()->getCommunication()->getClient()->getId()) ?
                    [
                        'id' => $notification->getId(),
                        'date' => date_format($notification->getDateCreated(), 'Y-m-d H:i'),
                        'read'   => $notification->isRead(),
                        'title' => $notification->getMessageCommunication()->getInitiateur()->getNom(),
                        'description' => $notification->getMessageCommunication()->getMessage(),
                        'type_notification' => 6,    'profile' => $this::BACK_END_URL . '/images/users/' . $profile,


                        'recepteur' => $notification->getInitiateur()->getKeySecret() != $notification->getRecepteur()->getKeySecret() ? $notification->getRecepteur()->getKeySecret() : 0
                        //  'recepteur' =>  $notification->getMessageCommunication()->getCommunication()->getClient()->getKeySecret(),

                    ] : null;

            default:
                # code...
                break;
        }
    }

    function  getIdShort(ShortComment  $comment)
    {
        if ($comment->getReferenceCommentaire() == null) {
            return    $comment->getShort()->getId();
        } else {
            return    $this->getIdShort($comment->getReferenceCommentaire());
        }
    }


    public function ListLikeShort(Short $short)
    {

        $likeList = $this->em->getRepository(ShortLike::class)->findBy(['short' => $short, 'like_short' => 1]);
        return $likeList;
    }


    public function ListCommentShort(Short $short)
    {

        $likeList = $this->em->getRepository(ShortComment::class)->findBy(['short' => $short]);
        return $likeList;
    }

    public function ListCommentComment(ShortComment $ShortComment)
    {

        $comList = $this->em->getRepository(ShortComment::class)->findBy(['reference_commentaire' => $ShortComment]);
        return $comList;
    }
    public function ListLikeCommentShort(ShortComment $shortComment)
    {

        $likeComList = $this->em->getRepository(ShortCommentLike::class)->findBy(['shortComment' => $shortComment, 'like_comment' => 1,]);
        return $likeComList;
    }


    public function ProduitModel(Produit $produit,   $user)
    {

        $lsImgP = [];
        $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
        foreach ($lProduitO  as $produit0) {
            $lsImgP[]
                = ['id' => $produit0->getId(), 'src' =>  $this::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
        }


        $produit =  [
            'id' => $produit->getId(),
            'like' => $this->isLike_Produit($produit->getId()),
            'islike' =>   $user == null ? false : $this->userlikeProduit($produit->getId(), $user),

            'codeProduit' => $produit->getCodeProduit(),
            'boutique' => $produit->getBoutique()->getTitre(),
            'description' => $produit->getDescription(),
            'titre' => $produit->getTitre(),
            'quantite' => $produit->getQuantite(),
            'prix' => $produit->getPrixUnitaire(),
            'status' => $produit->isStatus(),
            'date'
            => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
            'negociable' => $produit->isNegociable(),
            // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
            'images' => $lsImgP

        ];
        return $produit;
    }




    public function ShortModel(Short $short,   $user)
    {
        $boutique = $short->getBoutique();

        $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
        $limgB = [];

        foreach ($lBo  as $bo) {
            $limgB[]
                = ['id' => $bo->getId(), 'src' =>  $this::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
        }
        if (empty($limgB)) {
            $limgB[]
                = ['id' => 0, 'src' =>  $this::BACK_END_URL . '/images/default/boutique.png'];
        }
        $boutiqueU =  [
            'codeBoutique' => $boutique->getCodeBoutique(),
            'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
            'description' => $boutique->getDescription() ?? "Aucune",
            'titre' => $boutique->getTitre() ?? "Aucun",
            'status' => $boutique->isStatus(),
            'note' => $this->noteBoutique($boutique->getId()),

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


        $shortF =  [

            'id' => $short->getId(),
            'titre' => $short->getTitre() ?? "Aucun",
            'description' => $short->getDescription() ?? "Aucun",
            'status' => $short->isStatus(),
            'Preview' =>  $short->getPreview(),
            'is_like' =>   $user == null ? false : $this->userlikeShort($short, $user),
            'src' =>  $short->getSrc(),
            'codeShort' =>
            $short->getCodeShort(), 'nbre_like' => count($this->ListLikeShort($short)),
            'nbre_commentaire' => count($this->ListCommentShort($short)),
            'date' =>
            date_format($short->getDateCreated(), 'Y-m-d H:i'),
            'produits' =>  $this->ProduitForShort($short, $user),
            'boutique' =>  $boutiqueU

        ];


        return $shortF;
    }

    public function ProduitForShort(Short $short,   $user)
    {
        $listProduits = [];
        $listproduitsShort = $short->getListProduitShorts();
        if ($listproduitsShort) {
            foreach ($listproduitsShort as $produitShort) {
                $produit  = $produitShort->getProduit();
                $produitU =
                    $this->ProduitModel($produit, $user);
                array_push($listProduits, $produitU);
            }
        }

        return $listProduits;
    }
}
