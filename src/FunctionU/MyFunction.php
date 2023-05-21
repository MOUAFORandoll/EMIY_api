<?php

namespace App\FunctionU;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Commande;
use App\Entity\Commission;
use App\Entity\Compte;
use App\Entity\HistoriquePaiement;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
use App\Entity\NotationBoutique;
use App\Entity\NotationProduit;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
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

    private $em;
    private $client;
    private
        $token    = "sb.sX32rcaw0TdQopyWxXA0DwJTCOG0o2EA";
    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $client,


    ) {
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
                $this->getUniqueNameProduit();
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
        $price = ($produit->getPrix() -
            $produit->getPrix() * $commission->getPourcentageProduit() / 100
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
                $livreur = $commande->getListCommandeLivreurs()->getLivreur();
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
                            'token' => $response->toArray()["transaction"]['reference'], 'message' => 'Confirmer votre paiement',

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
    public function noteProduit($id)
    {
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);
        $notes = $this->em->getRepository(NotationProduit::class)->findBy(['produit' => $produit]);
        $noteL   = 0;
        foreach ($notes as   $note) {
            $noteL += $note->getNote();


            # code...
        }
        return

            count($notes) != 0 ?   $noteL / count($notes) : 0.0;
    }
    public function noteBoutique($id)
    {
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['id' => $id]);
        $notes = $this->em->getRepository(NotationBoutique::class)->findBy(['boutique' => $boutique]);
        $noteL   = 0;
        foreach ($notes as   $note) {
            $noteL += $note->getNote();


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
}
