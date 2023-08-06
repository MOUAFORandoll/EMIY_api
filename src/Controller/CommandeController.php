<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Commande;
use App\Entity\ListCommandeLivreur;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\Panier;
use App\Entity\Place;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use App\Entity\TypeCommande;
use App\Entity\TypeUser;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime;
use FFI\Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\UserPlateform;
use App\FunctionU\MyFunction;
use PHPUnit\TextUI\Command;
use Symfony\Component\Console\Command\ListCommand;

use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class CommandeController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $client;
    private $validator;
    private $myFunction;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,

        HttpClientInterface $client,

        ValidatorInterface $validator,
        MyFunction $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;

        $this->client = $client;


        $this->validator = $validator;
        $this->myFunction = $myFunction;
    }

    public function getUniqueCodeToken()
    {


        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $panier = $this->em->getRepository(Commande::class)->findOneBy(['token' => $chaine]);
        if ($panier) {
            return
                $this->getUniqueCodeToken();
        } else {
            return $chaine;
        }
    }


    public function getUniqueCode()
    {


        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 4; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['codeClient' => $chaine]);
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['codeCommande' => $chaine]);
        $lpp = $this->em->getRepository(ListProduitPanier::class)->findOneBy(['codeProduitPanier' => $chaine]);
        if ($commande ||  $lpp) {
            return
                $this->getUniqueCode();
        } else {
            return $chaine;
        }
    }


    /**
     * @Route("/commande/newX", name="commandeNewX", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeNewX(Request $request)
    {
        //
        // try {
        $data = $request->toArray();
        if (
            (empty($data['nom']) && empty($data['keySecret']))

            || (empty($data['phone']) && empty($data['keySecret']))
            || empty($data['listProduits'])
            || empty($data['idModePaiement'])
            // || empty($data['ville'])
            || empty($data['point_livraison'])

            || empty($data['longitude'])
            || empty($data['latitude'])

        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        // return new JsonResponse([
        //     'message' => $data['listProduits']
        // ], 400);
        $client = null;
        $codePanier =  'com' .
            $this->getUniqueCodeToken();
        // $montant = $pl->getVoyage()->getPrix();
        if (!empty($data['keySecret'])) {
            $client = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        }
        // if (!$client) {
        //     return new JsonResponse([
        //         'message' => 'Client inexistant '

        //     ], 203);
        // } 
        $nom = $data['nom'] ??   $client->getNom();
        $prenom = '';
        $phone = $data['phone'] ?? $client->getPhone();

        $modePaiement
            = $this->em->getRepository(ModePaiement::class)->findOneBy(['id' => $data['idModePaiement']]);
        $point_livraison
            = $this->em->getRepository(PointLivraison::class)->findOneBy(['id' => $data['point_livraison']]);

        /**
         * doit contenir l'id et la quantite de chaque produits
         */
        $listProduits = $data['listProduits'];
        // $produit = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $chaine]);
        if (!$listProduits) {
            return new JsonResponse([
                'message' => 'Selectionner des produits'

            ], 203);
        }

        $panier = new Panier();
        // $panier->setListProduits($data['listProduits']);
        $panier->setCodePanier($codePanier);
        $panier->setPrenomClient($prenom);
        $panier->setNomClient($nom);
        // $panier->setMontant($montant);
        $panier->setPhoneClient($phone);
        if ($client) {
            $panier->setUser($client);
        }
        $produits = [];

        $total = 0;
        $this->em->persist($panier);
        foreach ($listProduits as $prod) {
            $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $prod[0]]);
            if ($produit) {
                $lpp = new ListProduitPanier();
                $lpp->setPanier($panier);
                $lpp->setProduit($produit);
                $lpp->setQuantite($prod[1]);
                $lpp->setCodeProduitPanier($this->getUniqueCode());
                $this->em->persist($lpp);
                $produits[] = [
                    'nom' =>
                    $produit->getTitre(),
                    'quantite' => $prod[1],
                    'prix'
                    => $produit->getPrixUnitaire() *  $prod[1],
                ];
                $total += $produit->getPrixUnitaire() * $prod[1];
            }
        }

        // $ville = $data['ville'];
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];
        // $localisation = new Localisation();
        // $localisation->setVille(
        //     $ville
        // );
        // $localisation->setLongitude($longitude);
        // $localisation->setLatitude($latitude);
        // $this->em->persist($localisation);

        $commande = new Commande();
        $commande->setTitre(
            'Achat de produit'
        );
        $typeCommande = $this->em->getRepository(TypeCommande::class)->findOneBy(['id' => 1]);

        $commande->setDescription('Achat de produit');
        $commande->setModePaiement($modePaiement);
        $commande->setPanier($panier);
        // $commande->setLocalisation($localisation);
        $commande->setToken($codePanier);
        $commande->setCodeCommande($this->getUniqueCode());
        $commande->setCodeClient($this->getUniqueCode());
        $commande->setTypeCommande($typeCommande);
        $commande->setMontant($total);
        $commande->setStatusBuy(0);
        $commande->setPointLivraison($point_livraison);

        $this->em->persist($commande);

        $this->em->flush();

        return $this->myFunction->paid($data,  $total,  $commande->getId());
        // } catch (\Exception $e) {
        //     // Une erreur s'est produite, annulez la transaction
        //     
        //     return new JsonResponse([
        //         'message' => 'Une erreur est survenue'
        //     ], 203);
        // }
    }

    /**
     * @Route("/commande/negocie/new", name="commandeNewNegocie", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeNewNegocie(Request $request)
    {
        //
        // try {
        $data = $request->toArray();
        if (
            (empty($data['nom']) && empty($data['keySecret']))

            || (empty($data['phone']) && empty($data['keySecret']))
            || empty($data['listProduits'])
            || empty($data['idModePaiement'])
            || empty($data['ville'])
            || empty($data['point_livraison'])

            || empty($data['longitude'])
            || empty($data['latitude'])

        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        // return new JsonResponse([
        //     'message' => $data['listProduits']
        // ], 400);
        $client = null;
        $codePanier =  'com' .
            $this->getUniqueCodeToken();
        // $montant = $pl->getVoyage()->getPrix();
        if (!empty($data['keySecret'])) {
            $client = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        }
        // if (!$client) {
        //     return new JsonResponse([
        //         'message' => 'Client inexistant '

        //     ], 203);
        // } 
        $nom = $data['nom'] ??   $client->getNom();
        $prenom = '';
        $phone = $data['phone'] ?? $client->getPhone();

        $modePaiement
            = $this->em->getRepository(ModePaiement::class)->findOneBy(['id' => $data['idModePaiement']]);
        $point_livraison
            = $this->em->getRepository(PointLivraison::class)->findOneBy(['id' => $data['point_livraison']]);

        /**
         * doit contenir l'id , le prix unitaire et  la quantite de chaque produits
         */
        $produitNegocie = $data['produitNegocie'];

        if (!$produitNegocie) {
            return new JsonResponse([
                'message' => 'Selectionner des produits'

            ], 203);
        }

        $panier = new Panier();
        // $panier->setListProduits($data['produitNegocie']);
        $panier->setCodePanier($codePanier);
        $panier->setPrenomClient($prenom);
        $panier->setNomClient($nom);
        // $panier->setMontant($montant);
        $panier->setPhoneClient($phone);
        if ($client) {
            $panier->setUser($client);
        }
        $produits = [];

        $total = 0;
        $this->em->persist($panier);

        $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $produitNegocie[0]]);
        if ($produit) {
            $lpp = new ListProduitPanier();
            $lpp->setPanier($panier);
            $lpp->setProduit($produit);
            $lpp->setQuantite($produitNegocie[1]);
            $lpp->setPrixUnitaireVente($produitNegocie[2]);
            $lpp->setCodeProduitPanier($this->getUniqueCode());
            $this->em->persist($lpp);
            $produits[] = [
                'nom' =>
                $produit->getTitre(),
                'quantite' => $produitNegocie[1],
                'prix'
                => $produitNegocie[2] *  $produitNegocie[1],
            ];
            $total += $produitNegocie[2] * $produitNegocie[1];
        }


        $commande = new Commande();
        $commande->setTitre(
            'Achat de produit'
        );
        $typeCommande = $this->em->getRepository(TypeCommande::class)->findOneBy(['id' => 2]);

        $commande->setDescription('Achat de produit');
        $commande->setModePaiement($modePaiement);
        $commande->setPanier($panier);
        // $commande->setLocalisation($localisation);
        $commande->setToken($codePanier);
        $commande->setCodeCommande($this->getUniqueCode());
        $commande->setCodeClient($this->getUniqueCode());
        $commande->setTypeCommande($typeCommande);

        $commande->setStatusBuy(0);
        $commande->setPointLivraison($point_livraison);

        $this->em->persist($commande);

        $this->em->flush();

        return $this->myFunction->paid($data,  $total,  $commande->getId());
    }


    /**
     * @Route("/commande/verify", name="verifyCommande", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function verifyCommande(Request $request)
    {
        /**
         * request doit contenir  modePaiement, token,idListSmsAchete, quantite
         */
        $data = $request->toArray();

        if (empty($data['id'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer veuillez contacter le developpeur'
            ], 400);
        }
        $id = $data['id'];

        $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $id]);



        if (

            $commande
        ) {
            $statusCommande =   $this->myFunction->verifyBuy($commande->getToken());
            if ($statusCommande == true) {

                $lpp      =
                    $commande->getPanier()->getListProduitPaniers();
                $produits = [];

                $total = 0;

                foreach ($lpp  as $prod) {
                    $produit =
                        $prod->getProduit();
                    if ($produit) {

                        $produits[] = [
                            'nom' =>
                            $produit->getTitre(),
                            'quantite' => $prod->getQuantite(),
                            'prix'
                            => $produit->getPrixUnitaire() * $prod->getQuantite(),
                        ];
                        $total += $produit->getPrixUnitaire() * $prod->getQuantite();
                    }
                }

                $dataPrint = [
                    'nom' =>
                    $commande->getPanier()->getNomClient(),
                    'total'
                    =>    $total,
                    'date' =>  date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                    'produits' =>  $produits
                ];
                $pdf = $this->GeneratePdf($dataPrint);

                $commande->setStatusBuy(true);

                $this->em->persist($commande);






                $this->em->flush();
                return new JsonResponse([
                    'status'
                    => true,
                    'pdf' =>  $pdf,
                    'id' =>  $commande->getId(),
                    'codeClient'  => $commande->getCodeClient(),
                    'codeCommande' =>  $commande->getCodeCommande(),
                    'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                    'message' => 'Achat Effectue ,Votre Livraison est en cours, veuillez patienter'
                ], 201);
            } else {

                $this->em->flush();
                return new JsonResponse([
                    'status'
                    => false,
                    'message' => 'En attente de validation de votre part'
                ], 200);
            }
        } else {
            return new JsonResponse([
                'status'
                => false,
                'message' => 'Une erreur est survenue'
            ], 203);
        }
    }


    /**
     * @Route("/commande/livreur/set", name="commandeSetLivreur", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeSetLivreur(Request $request)
    {
        $data = $request->toArray();
        if (
            (empty($data['keySecret']))
            || (empty($data['codeCommande']))

        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        // return new JsonResponse([


        $commande =
            $this->em->getRepository(Commande::class)->findOneBy(['codeCommande' => $data['codeCommande']]);


        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        if (

            $commande
        ) {
            if (

                $livreur
            ) {

                $n =   $this->nmbreCommLivreur($livreur->getId());
                if ($n < 2) {

                    $lcl =                new ListCommandeLivreur();

                    $lcl->setCommande($commande);
                    $lcl->setLivreur($livreur);
                    $commande->setStatusFinish(1);
                    $this->em->persist($commande);

                    $this->em->persist($lcl);




                    $this->em->flush();
                    return new JsonResponse([
                        'message' => 'La commande vous est attribuee',
                        // // 'a' =>  $panier->getListProduits(),
                        // 'id' =>  $commande->getId(),                                    'codeClient'
                        // => $commande->getCodeClient(),
                        // 'codeCommande' =>  $commande->getCodeCommande(),
                        // 'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i')

                    ], 200);
                } else {
                    return new JsonResponse([
                        'message' => 'Veuillez terminer vos precedentes livraisons'

                    ], 203);
                }
            } else {
                return new JsonResponse([
                    'message' => 'Vous ne pouvez pas acceder acette commande, veuillez joindre un administrateur'

                ], 203);
            }
        } else {
            return new JsonResponse([
                'message' => 'Commande introuvable, veuillez joindre un administrateur'

            ], 203);
        }
    }
    /**
     * @Route("/commande/new", name="commandeNew", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeNew(Request $request)
    {
        $data = $request->toArray();

        if (
            (empty($data['nom']) && empty($data['keySecret']))
            || (empty($data['prenom']) && empty($data['keySecret']))
            || (empty($data['phone']) && empty($data['keySecret']))
            || empty($data['listProduits'])
            || empty($data['idModePaiement'])
        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }


        $codePanier =  'com' .
            $this->getUniqueCodeToken();

        $client = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        $nom = $data['nom'] ??   $client->getNom();
        $prenom = $data['prenom'] ??   $client->getPrenom();
        $phone = $data['phone'] ?? $client->getPhone();
        $modePaiement
            = $this->em->getRepository(ModePaiement::class)->findOneBy(['id' => $data['idModePaiement']]);

        $listProduits = $data['listProduits'];
        if (!$listProduits) {
            return new JsonResponse([
                'message' => 'Selectionner des produits'

            ], 203);
        }

        $panier = new Panier();

        $panier->setCodePanier($codePanier);
        $panier->setPrenomClient($prenom);
        $panier->setNomClient($nom);

        $panier->setPhoneClient($phone);
        if ($client) {
            $panier->setUser($client);
        }

        $this->em->persist($panier);

        foreach ($listProduits as $prod) {
            $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $prod[0]]);
            if ($produit) {
                $lpp = new ListProduitPanier();
                $lpp->setPanier($panier);
                $lpp->setProduit($produit);
                $lpp->setQuantite($prod[1]);
                $lpp->setCodeProduitPanier($this->getUniqueCode());

                $this->em->persist($lpp);
            }
        }


        $ville = $data['ville'];
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];
        $localisation = new Localisation();
        $localisation->setVille(
            $ville
        );
        $localisation->setLongitude($longitude);
        $localisation->setLatitude($latitude);
        $this->em->persist($localisation);

        $commande = new Commande();
        $commande->setTitre(
            'Achat de produit'
        );
        $commande->setDescription('Achat de produit');
        $commande->setModePaiement($modePaiement);
        $commande->setPanier($panier);
        // $commande->setLocalisation($localisation);
        $commande->setToken($codePanier);
        $commande->setCodeCommande($this->getUniqueCode());
        $commande->setCodeClient($this->getUniqueCode());


        $commande->setStatusBuy(1);

        $this->em->persist($commande);






        $this->em->flush();

        return new JsonResponse([
            'message' => 'Valider votre paiement',
            // 'a' =>  $panier->getListProduits(),
            'commande' =>  $commande->getId()

        ], 200);
    }



    /**
     * @Route("/commande/boutique/read", name="comandeReadBoutique", methods={"GET"})
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
    public function comandeReadBoutique(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);


        if (
            /*     empty($data['keySecret']) || */
            empty($request->get('codeBoutique'))
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }


        $codeBoutique = $request->get('codeBoutique');
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $lP = [];
        $lcom = $this->em->getRepository(Commande::class)->findAll();
        foreach ($lcom  as $commande) {


            if ($commande->getPanier()) {
                $panier = $commande->getPanier();

                $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);

                if ($listProduitPanier) {
                    foreach ($listProduitPanier  as $pp) {

                        $produit = $pp->getProduit();

                        if ($produit) {

                            if ($produit->getBoutique()) {

                                if (
                                    $produit->getBoutique()->getCodeBoutique() == $codeBoutique &&
                                    !$pp->isStatus()
                                ) {

                                    $lsImgP = [];
                                    $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                    foreach ($lProduitO  as $produit0) {
                                        $lsImgP[]
                                            = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                    }

                                    $produit =  [
                                        'codeProduit' => $produit->getCodeProduit(),
                                        'codeCommande' => $commande->getCodeCommande(),
                                        'numCommande' => 'Comm' . $commande->getId(),

                                        'titre' => $produit->getTitre(),
                                        'prix' => $produit->getPrixUnitaire(),
                                        'quantite' => $pp->getQuantite(),
                                        'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                        'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                                        'photo' => $lsImgP[0]
                                    ];
                                    array_push($lP, $produit);
                                }
                            }
                        }
                    }
                }
                // $listProduits = $serializer->serialize($lP, 'json');



            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $lP
                ],
                200
            );
    }

    /**
     * @Route("/commande/boutique/readH", name="comandeReadH", methods={"GET"})
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
    public function comandeReadH(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;




        if (
            /*     empty($data['keySecret']) || */
            empty($request->get('codeBoutique'))
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }


        $codeBoutique = $request->get('codeBoutique');

        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $lcom = $this->em->getRepository(Commande::class)->findAll();
        $lP = [];
        foreach ($lcom  as $commande) {
            if ($commande) {
                $panier = $commande->getPanier();

                if ($panier) {

                    $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);

                    if ($listProduitPanier) {
                        foreach ($listProduitPanier  as $pp) {

                            $produit = $pp->getProduit();

                            if ($produit) {

                                if ($produit->getBoutique()) {

                                    if (
                                        $produit->getBoutique()->getCodeBoutique() == $codeBoutique &&
                                        $pp->isStatus() == 1
                                    ) {

                                        $lsImgP = [];
                                        $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                        foreach ($lProduitO  as $produit0) {
                                            $lsImgP[]
                                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                        }

                                        $produit =  [
                                            // 'codeProduit' => $produit->getCodeProduit(),
                                            'codeCommande' => $commande->getCodeCommande(),
                                            'numCommande' => 'Com' . $commande->getId(),                                            'titre' => $produit->getTitre(),
                                            'prix' => $produit->getPrixUnitaire(),
                                            'quantite' => $pp->getQuantite(),
                                            'status' => 'Vendu',

                                            'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                                            'photo' => $lsImgP[0]
                                        ];
                                        array_push($lP, $produit);
                                    }
                                }
                            }
                        }
                    }
                    // $listProduits = $serializer->serialize($lP, 'json');


                }
            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $lP,
                    'codeBoutique' => $codeBoutique
                ],
                200
            );
    }


    /**
     * @Route("/commande/livreur/finish", name="comandeReadLivreurFinish", methods={"POST"})
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
    public function comandeReadLivreurFinish(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            empty($data['keySecret'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$livreur) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        if ($livreur->getTypeUser()->getId() != 3) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }

        $llcom
            = $this->em->getRepository(ListCommandeLivreur::class)->findBy(['livreur' => $livreur]);

        $fCom = [];
        foreach ($llcom  as $lcom) {
            $commande = $lcom->getCommande();

            if ($commande) {
                if ($commande->getStatusFinish() == 3) {
                    $panier = $commande->getPanier();

                    if ($panier) {

                        $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);
                        $lP = [];
                        if ($listProduitPanier) {

                            foreach ($listProduitPanier  as $pp) {

                                $produit = $pp->getProduit();

                                if ($produit) {




                                    if (
                                        $produit->getBoutique()
                                    ) {

                                        $lsImgP = [];
                                        $lProduitO =   $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                        foreach ($lProduitO  as $produit0) {
                                            $lsImgP[]
                                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                        }
                                        $boutique = [

                                            'id'  => $produit->getBoutique()->getId(),

                                            'titre'
                                            => $produit->getBoutique()->getTitre(),
                                            'codeBoutique'
                                            => $produit->getBoutique()->getCodeBoutique(),
                                            'description'
                                            => $produit->getBoutique()->getDescription(),

                                            'localisation' =>  $produit->getBoutique()->getLocalisation() ? [
                                                'ville' =>
                                                $produit->getBoutique()->getLocalisation()->getVille(),

                                                'longitude' =>
                                                $produit->getBoutique()->getLocalisation()->getLongitude(),
                                                'latitude' =>
                                                $produit->getBoutique()->getLocalisation()->getLatitude(),
                                            ] : []
                                        ];
                                        $produit =  [
                                            'idBoutique'  => $produit->getBoutique()->getId(),
                                            'codeProduit' => $pp->getCodeProduitPanier(),
                                            // 'codeCommande' => $commande->getCodeCommande(),
                                            'boutique' => $boutique,
                                            'titre' => $produit->getTitre(),
                                            'prix' => $produit->getPrixUnitaire(),
                                            'quantite' => $pp->getQuantite(),
                                            'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                            'photo' => $lsImgP[0]
                                        ];
                                        array_push($lP, $produit);
                                    }
                                }
                            }
                        }
                    }
                    $lff = [];
                    foreach ($lP  as $p) {

                        foreach ($lP  as $y) {
                            if ($p['idBoutique'] == $y['idBoutique']) {
                                if (!in_array($y, $lff))
                                    array_push($lff, $y);
                            }
                        }
                    }


                    if (count($lP) > 0) {
                        $cf = [
                            'etape' =>  'Commande livree',
                            'codeCommande' => 'Com' . $commande->getId(),
                            'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),

                            'produits' => $lff,
                            'localisation' =>  $commande->getLocalisation() ? [
                                'ville' =>
                                $commande->getLocalisation()->getVille(),

                                'longitude' =>
                                $commande->getLocalisation()->getLongitude(),
                                'latitude' =>
                                $commande->getLocalisation()->getLatitude(),
                            ] : []
                        ];
                        array_push($fCom, $cf);
                    }
                    // $listProduits = $serializer->serialize($lP, 'json');


                }
            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $fCom
                ],
                200
            );
    }


    /**
     * @Route("/commande/nolivreur/read", name="comandeReadNoLivreur", methods={"POST"})
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
    public function comandeReadNoLivreur(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            empty($data['keySecret'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$livreur) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        if ($livreur->getTypeUser()->getId() != 3) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }

        $llcom
            = $this->em->getRepository(Commande::class)->findAll();

        $fCom = [];
        foreach ($llcom  as $commande) {


            if ($commande) {
                if ($commande->getStatusFinish() == 0) {

                    $llcom
                        = $this->em->getRepository(ListCommandeLivreur::class)->findBy(['commande' => $commande]);
                    if (!$llcom) {
                        $panier = $commande->getPanier();

                        if ($panier) {

                            $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);
                            $lP = [];
                            if ($listProduitPanier) {

                                foreach ($listProduitPanier  as $pp) {

                                    $produit = $pp->getProduit();

                                    if ($produit) {




                                        if (
                                            $produit->getBoutique()
                                        ) {

                                            $lsImgP = [];
                                            $lProduitO =   $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                            foreach ($lProduitO  as $produit0) {
                                                $lsImgP[]
                                                    = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                            }
                                            $boutique = [

                                                'id'  => $produit->getBoutique()->getId(),

                                                'titre'
                                                => $produit->getBoutique()->getTitre(),
                                                'codeBoutique'
                                                => $produit->getBoutique()->getCodeBoutique(),
                                                'description'
                                                => $produit->getBoutique()->getDescription(),

                                                'localisation' =>  $produit->getBoutique()->getLocalisation() ? [
                                                    'ville' =>
                                                    $produit->getBoutique()->getLocalisation()->getVille(),

                                                    'longitude' =>
                                                    $produit->getBoutique()->getLocalisation()->getLongitude(),
                                                    'latitude' =>
                                                    $produit->getBoutique()->getLocalisation()->getLatitude(),
                                                ] : []
                                            ];
                                            $produit =  [
                                                'idBoutique'  => $produit->getBoutique()->getId(),
                                                'codeProduit' => $pp->getCodeProduitPanier(),
                                                // 'codeCommande' => $commande->getCodeCommande(),
                                                'boutique' => $boutique,
                                                'titre' => $produit->getTitre(),
                                                'prix' => $produit->getPrixUnitaire(),
                                                'quantite' => $pp->getQuantite(),
                                                'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                                'photo' => $lsImgP[0]
                                            ];
                                            array_push($lP, $produit);
                                        }
                                    }
                                }
                            }
                        }
                        $lff = [];
                        foreach ($lP  as $p) {

                            foreach ($lP  as $y) {
                                if ($p['idBoutique'] == $y['idBoutique']) {
                                    if (!in_array($y, $lff))
                                        array_push($lff, $y);
                                }
                            }
                        }


                        if (count($lP) > 0) {
                            $cf = [
                                'etape' =>   $commande->getStatusFinish() == 0 ? 'En attente' : '',
                                'codeCommande' => 'Com' . $commande->getId(),
                                'codeCommandeS' =>  $commande->getCodeCommande(),
                                'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),

                                'produits' => $lff,
                            ];
                            array_push($fCom, $cf);
                        }
                        // $listProduits = $serializer->serialize($lP, 'json');


                    }
                }
            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $fCom
                ],
                200
            );
    }


    /**
     * @Route("/commande/livreur/read", name="comandeReadLivreur", methods={"POST"})
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
    public function comandeReadLivreur(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;




        if (
            empty($data['keySecret'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Veuillez recharger la page et reessayer   '
                ],
                400
            );
        }

        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$livreur) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        if ($livreur->getTypeUser()->getId() != 3) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }

        $llcom
            = $this->em->getRepository(ListCommandeLivreur::class)->findBy(['livreur' => $livreur]);

        $fCom = [];
        foreach ($llcom  as $lcom) {
            $commande = $lcom->getCommande();

            if ($commande) {
                if ($commande->getStatusFinish() == 1) {
                    $panier = $commande->getPanier();

                    if ($panier) {

                        $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);
                        $lP = [];
                        if ($listProduitPanier) {

                            foreach ($listProduitPanier  as $pp) {

                                $produit = $pp->getProduit();

                                if ($produit) {




                                    if (
                                        $produit->getBoutique()
                                    ) {

                                        $lsImgP = [];
                                        $lProduitO =   $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                        foreach ($lProduitO  as $produit0) {
                                            $lsImgP[]
                                                = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                        }
                                        $boutique = [

                                            'id'  => $produit->getBoutique()->getId(),

                                            'titre'
                                            => $produit->getBoutique()->getTitre(),
                                            'codeBoutique'
                                            => $produit->getBoutique()->getCodeBoutique(),
                                            'description'
                                            => $produit->getBoutique()->getDescription(),

                                            'localisation' =>  $produit->getBoutique()->getLocalisation() ? [
                                                'ville' =>
                                                $produit->getBoutique()->getLocalisation()->getVille(),

                                                'longitude' =>
                                                $produit->getBoutique()->getLocalisation()->getLongitude(),
                                                'latitude' =>
                                                $produit->getBoutique()->getLocalisation()->getLatitude(),
                                            ] : []
                                        ];
                                        $produit =  [
                                            'idBoutique'  => $produit->getBoutique()->getId(),
                                            'codeProduit' => $pp->getCodeProduitPanier(),
                                            // 'codeCommande' => $commande->getCodeCommande(),
                                            'boutique' => $boutique,
                                            'titre' => $produit->getTitre(),
                                            'prix' => $produit->getPrixUnitaire(),
                                            'quantite' => $pp->getQuantite(),
                                            'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                            'photo' => $lsImgP[0]
                                        ];
                                        array_push($lP, $produit);
                                    }
                                }
                            }
                        }
                    }
                    $lff = [];
                    foreach ($lP  as $p) {

                        foreach ($lP  as $y) {
                            if ($p['idBoutique'] == $y['idBoutique']) {
                                if (!in_array($y, $lff))
                                    array_push($lff, $y);
                            }
                        }
                    }


                    if (count($lP) > 0) {
                        $cf = [
                            'etape' =>   'Recuperation boutique',
                            'codeCommande' => 'Com' . $commande->getId(),
                            'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),

                            'produits' => $lff,
                            'localisation' =>  $commande->getLocalisation() ? [
                                'ville' =>
                                $commande->getLocalisation()->getVille(),

                                'longitude' =>
                                $commande->getLocalisation()->getLongitude(),
                                'latitude' =>
                                $commande->getLocalisation()->getLatitude(),
                            ] : [
                                'ville' =>
                                'Aucune',

                                'longitude' =>
                                0,
                                'latitude' =>
                                0,
                            ]
                        ];
                        array_push($fCom, $cf);
                    }
                    // $listProduits = $serializer->serialize($lP, 'json');


                }
            }
        }
        return
            new JsonResponse(
                [

                    'data'
                    =>  $fCom
                ],
                200
            );
    }


    /**
     * @Route("/commande/validateproduit/boutique", name="commandeVB", methods={"POST"})
     * @param array $data doit contenir  
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeVB(Request $request)
    {
        $data = $request->toArray();

        if (
            empty($data['codeProduitPanier'])
            ||
            empty($data['codeBoutique'])
            ||  empty($data['codeCommande']) || empty($data['keySecret'])

        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }

        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        if (!$livreur) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        if ($livreur->getTypeUser()->getId() != 3) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['codeCommande' => $data['codeCommande']]);
        if (!$commande) {
            return new JsonResponse([
                'message' => 'Commande introuvable, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        $lcl = $this->em->getRepository(ListCommandeLivreur::class)->findOneBy(['commande' => $commande, 'livreur' => $livreur]);

        if (!$lcl) {
            return new JsonResponse([
                'message' => 'Cette livraison ne vous est pas affectee, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }

        $listPP = $this->em->getRepository(ListProduitPanier::class)->findOneBy(['codeProduitPanier' => $data['codeProduitPanier']]);
        if ($listPP) {
            $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' =>  $listPP->getProduit()->getId()]);
            if ($produit->getBoutique()->getCodeBoutique() == $data['codeBoutique']) {
                if ($produit->getQuantite() -  $listPP->getQuantite() < 0) {
                    return new JsonResponse([
                        'message' => 'Quantite de produit ne permet pas la livraison contacter l\'administrateur, vous ne pouvez pas poursuivre l\'operation'

                    ], 203);
                }
                $listPP->setStatus(1);

                $produit->setQuantite($produit->getQuantite() -  $listPP->getQuantite());
                $this->em->persist($produit);
                $this->em->persist($listPP);
                $this->em->flush();
                $this->commandeIsOk($data['codeCommande']);
                $this->myFunction->buyCommissionProduitBoutique($listPP->getId(),        $commande->getId());
                return new JsonResponse([
                    'message' => 'Produit Recuperer',


                ], 200);
            } else {
                return new JsonResponse([
                    'message' => 'Verifier les infromation entres',


                ], 203);
            }
        } else {
            return new JsonResponse([
                'message' => 'Produit introuvable',


            ], 203);
        }
    }
    /**
     * @Route("/commande/validateproduit/client", name="commandeVC", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeVC(Request $request)
    {
        $data = $request->toArray();

        if (
            empty($data['codeCommande'])
            ||
            empty($data['codeClient']) || empty($data['keySecret'])

        ) {
            return new JsonResponse([
                'message' =>  'Veuillez recharger la page et reessayer   '
            ], 400);
        }

        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$livreur) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        if ($livreur->getTypeUser()->getId() != 3) {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas un livreur, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }

        $commande = $this->em->getRepository(Commande::class)->findOneBy(['codeCommande' => $data['codeCommande'],/*  'codeBoutique' => $data['codeBoutique'],  */ 'codeClient' => $data['codeClient']]);
        if (!$commande) {
            return new JsonResponse([
                'message' => 'Commande introuvable, vous ne pouvez pas poursuivre l\'operation'

            ], 203);
        }
        $listCommandes = $this->em->getRepository(ListCommandeLivreur::class)->findOneBy(['commande' => $commande, 'livreur' => $livreur]);
        if ($listCommandes) {
            $commande->setStatusFinish(3);
            $this->em->persist($commande);

            $this->em->flush();

            return new JsonResponse([
                'message' => 'Commande livree',


            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Vous n\'etes pas le responsable de cette livraison',


            ], 203);
        }
    }
    /**
     * @Route("/commande/validate/actualise", name="commandeActualise", methods={"GET"})
     * @param  
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeActualise()
    {
        $ok = true;
        $lcommande = $this->em->getRepository(Commande::class)->findAll();
        foreach ($lcommande  as $commande) {
            if ($commande) {
                $panier
                    =  $commande->getPanier();
                if ($panier) {
                    $lpp =   $panier->getListProduitPaniers();
                    foreach ($lpp  as $pp) {

                        if ($pp->isStatus() == false) {
                            $ok = false;
                        }
                    }
                }
            }
            if ($ok) {
                $commande->setStatusFinish(2);


                $this->em->persist($commande);
                $this->em->flush();
            }
        }
        return new JsonResponse([
            'message' =>  'Actualise'
        ], 200);
    }

    public function commandeIsOk($codeCommande)
    {
        $ok = true;
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['codeCommande' => $codeCommande]);
        if ($commande) {
            $panier
                =  $commande->getPanier();
            if ($panier) {
                $lpp =   $panier->getListProduitPaniers();
                foreach ($lpp  as $pp) {

                    if ($pp->isStatus() == false) {
                        $ok = false;
                    }
                }
            }
        }
        if ($ok) {
            $commande->setStatusFinish(2);
            $this->myFunction->buyCommissionLivreur($commande->getId());
            $this->em->persist($commande);
            $this->em->flush();
        }
    }

    public function nmbreCommLivreur($idLivreur)
    {
        $livreur = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $idLivreur]);

        $num = 0;
        $llcom
            = $this->em->getRepository(ListCommandeLivreur::class)->findBy(['livreur' => $livreur]);

        $fCom = [];
        foreach ($llcom  as $lcom) {
            $commande = $lcom->getCommande();

            if ($commande) {
                if ($commande->getStatusFinish() != 3) {
                    $num++;
                }
            }
        }
        return $num;
    }




    /**
     * @Route("/commande/produit/read/user", name="commandeProduitReadForUser", methods={"POST"})
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
     * @param array $data doit contenir l'id de la commande'
     * 
     * 
     */
    public function commandeProduitReadForUser(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;
        if (empty($data['idCom'])) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer  '
            ], 400);
        }




        $commande = $this->em->getRepository(Commande::class)->findOneBy(['id' => $data['idCom']]);



        if ($commande) {
            // if ($commande->getStatusFinish() == 0 || $commande->getStatusFinish() == 1) {
            $panier = $commande->getPanier();

            if ($panier) {

                $listProduitPanier = $this->em->getRepository(ListProduitPanier::class)->findBy(['panier' => $panier]);
                $lP = [];
                if ($listProduitPanier) {

                    foreach ($listProduitPanier  as $pp) {

                        $produit = $pp->getProduit();

                        if ($produit) {




                            if (
                                $produit->getBoutique()
                            ) {

                                $lsImgP = [];
                                $lProduitO =   $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
                                foreach ($lProduitO  as $produit0) {
                                    $lsImgP[]
                                        = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
                                }
                                $boutique = [

                                    'id'  => $produit->getBoutique()->getId(),

                                    'titre'
                                    => $produit->getBoutique()->getTitre(),
                                    'codeBoutique'
                                    => $produit->getBoutique()->getCodeBoutique(),
                                    'description'
                                    => $produit->getBoutique()->getDescription(),

                                ];
                                $produit =  [
                                    'codeProduit' => $pp->getCodeProduitPanier(),
                                    // 'codeCommande' => $commande->getCodeCommande(),
                                    'boutique' => $boutique,
                                    'titre' => $produit->getTitre(),
                                    'prix' => $produit->getPrixUnitaire(),
                                    'quantite' => $pp->getQuantite(),
                                    'status' => $pp->isStatus() ? 'Valide' : 'En cours',

                                    'photo' => $lsImgP[0]
                                ];
                                array_push($lP, $produit);
                            }
                        }
                    }
                }
            }



            return
                new JsonResponse([
                    'data'
                    =>
                    $lP,

                ], 200);
        } else {
            return
                new JsonResponse([
                    'data'
                    =>
                    [],

                ], 200);
        }
    }


    /**
     * @Route("/commande/read/acheteur", name="commandeReadAcheteur", methods={"POST"})
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
     * @param array $data doit contenir la  la keySecretAcheteur de l'acheteur
     * 
     * 
     */
    public function commandeReadAcheteur(Request $request)
    {
        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);
        $data = $request->toArray();
        $possible = false;
        if (empty($data['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayerveuillez preciser votre keySecret '
            ], 400);
        }
        $vendeur = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if ($vendeur) {



            if (
                $vendeur->getTypeUser()->getId() == 4
            ) {

                $lCommande = $this->em->getRepository(Commande::class)->findBy(['acheteur' =>  $vendeur]);

                if ($lCommande) {

                    $lB = [];
                    foreach ($lCommande  as $cl) {

                        $commande =  [
                            // 'id' => $cl->getId(),
                            // 'phone' => $cl->getNumeroClient(),
                            // 'prenom' => $cl->getPrenom(),
                            // 'nom' => $cl->getNomClient(),

                            // 'boutique' => $cl->getPlace()->getVoyage()->getPointDeVente()->getBoutique()->getNom(),
                            // 'pointDeVente' => $cl->getPlace()->getVoyage()->getPointDeVente()->getNomPointDeVente(),
                            // 'typeCommande' => $cl->getPlace()->getVoyage()->getTypeVoyage()->getLibelle(),

                            // 'numeroSiege' => $cl->getPlace()->getNumeroPlace(),

                            // 'date' => date_format($cl->getDateCreate(), 'Y-m-d H:i')
                        ];
                        array_push($lB, $commande);
                    }
                    // $listCommandes = $serializer->serialize($lB, 'json');

                    return
                        new JsonResponse(
                            [
                                'data'
                                =>  $lB
                            ],
                            200
                        );
                } else {
                    return
                        new JsonResponse([
                            'data'
                            => [],
                            'message' => 'Aucune commande'
                        ], 200);
                }
            } else {
                return new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Action impossible'
                ], 203);
            }
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Utilisateur introuvable'
            ], 400);
        }
    }

    /**
     * @Route("/commande/update", name="commandeUpdate", methods={"POST"})
     * @param array $data doit contenir la keySecretServeuse ou keySecretClient
     * @param Request $request
     * @return JsonResponse
     */
    public function commandeUpdate(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecretAdmin']) || empty($data['keySecretUser']) || empty($data['typeUser'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        $admin
            = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecretAdmin']]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecretUser']]);

        if (
            !$user
            || !$admin
        ) {
            return new JsonResponse([
                'message' => 'Desolez une erreur est survenue durant l\'operation'
            ], 400);
        }

        if (
            $admin->getTypeUser()->getId() == 1
        ) {
            $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => $data['typeUser']]);

            $user->setTypeUser($typeUser);
        }

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Type Utilisateur ' . $typeUser->getLibelle() . ' affecte avec success a ' . $user->getNom()

        ], 200);
    }

    /**
     * @Route("/pdf", name="Pdf", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function Pdf(Request $request)
    {

        $dataPrint = [
            'nom' =>
            'mOUAFO',
            'total'
            =>    150150,
            'date' =>  date_format(new \DateTimeImmutable(), 'Y-m-d H:i'),
            'produits' => ['']
        ];
        $pdf = $this->myFunction->verifyBuy('CXOCWmCmeOrL7P1bIbsvVszSLFgrx5Nx');
        return new JsonResponse([
            'PDF' => $pdf

        ], 200);
    }

    public function GeneratePdf($data)
    {

        // Récupérer les données nécessaires pour générer le PDF


        // Créer une instance de PDF avec la bibliothèque de votre choix (ex : Dompdf)
        $pdf = new Dompdf();

        $contenu = '';
        for ($i = 0; $i < count($data['produits']); $i++) {
            $contenu = "<tr>
                    <td>" . $data['produits'][$i]['nom']  . "</td>
                    <td>" .  $data['produits'][$i]['quantite']  . "</td>
                    <td>FCFA " .  $data['produits'][$i]['prix']  . "</td>
                </tr>\n";
        }

        // Générer le contenu du PDF avec les données récupérées
        $html = $this->renderView('pdf/index.html.twig', [

            'nom' => $data['nom'],
            'total' => $data['total'],
            'date' => $data['date'],
            'data' =>  $contenu
        ]);
        $pdf->loadHtml($html);

        // Rendre le PDF
        $pdf->render();

        // Enregistrer le PDF dans un dossier web accessible au public
        $fileName = 'facture_' .
            str_replace('-', '',    str_replace(':', '',    preg_replace('/\s+/', '', $data['nom'])))  . '_' .
            str_replace('-', '',    str_replace(':', '',    preg_replace('/\s+/', '', $data['date']))) . '.pdf';
        $publicPath = $this->getParameter('kernel.project_dir') . '/public/factures';
        // $pdf->output($publicPath . '/' . $fileName);
        file_put_contents($publicPath . '/' . $fileName, $pdf->output());
        // Créer le lien de téléchargement du fichier PDF
        $downloadUrl = $this->generateUrl('download_pdf', [
            'fileName' =>  $fileName,

        ]);

        // Retourner le lien de téléchargement du fichier PDF
        return  $downloadUrl;
    }
    /**
     * @Route("/download-pdf/{fileName}", name="download_pdf")
     */
    public function downloadPdf(string $fileName)
    {
        // Récupérer le chemin complet du fichier PDF
        $publicPath = $this->getParameter('kernel.project_dir') . '/public/factures';
        $filePath = $publicPath . '/' . $fileName;

        // Créer une réponse Symfony à partir du fichier PDF
        $response = new Response(file_get_contents($filePath));

        // Définir les entêtes pour la réponse
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');

        return $response;
    }
}
