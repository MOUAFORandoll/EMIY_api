<?php

namespace App\Controller;

use App\Entity\AbonnementBoutique;
use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\ListProduitPanier;
use App\Entity\Localisation;
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
use Knp\Component\Pager\PaginatorInterface;
use App\FunctionU\MyFunction;

class AbonnementBoutiqueController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $paginator;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        PaginatorInterface $paginator,
        MyFunction  $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->paginator = $paginator;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/abonnement", name="abonnementAdd", methods={"POST"})
     *  @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     */
    public function abonnementAdd(Request $request,)
    {

        $data
            =        $data = $request->toArray();


        if (empty($data['codeBoutique'])   || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   ',

            ], 400);
        }

        $codeBoutique = $data['codeBoutique'];

        $keySecret = $data['keySecret'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);
        if (!$user || !$boutique) {
            return new JsonResponse([
                'message' => 'Compte introuvable'

            ], 203);
        }
        $abonnementExist = $this->em->getRepository(AbonnementBoutique::class)->findOneBy(['boutique' => $boutique]);
        if ($abonnementExist) {

            $abonnementExist->setStatus(!$abonnementExist->isStatus());
            $this->em->persist($abonnementExist);
        } else {
            $abonnement = new AbonnementBoutique();

            $abonnement->setClient($user);
            $abonnement->setBoutique($boutique);
            $this->em->persist($abonnement);
        }
        $this->em->flush();
        return new JsonResponse([
            'message' => 'Success',
            'status' => true,
            'codeBoutique' =>  $boutique->getCodeBoutique()

        ], 200);
    }

    /**
     * @Route("/abonnement/user", name="AbonnementReadClient", methods={"GET"})
     *  @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     */
    public function AbonnementReadClient(Request $request,)
    {
        $keySecret = $request->query->get('keySecret');
        $page = $request->query->get('page') == null || $request->query->get('page') == 0 ? 1 : $request->query->get('page');




        // return new JsonResponse([

        //     'd' => $data
        // ], 400);
        if (empty($keySecret)) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   ',

            ], 400);
        }





        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $abonnement = $this->em->getRepository(AbonnementBoutique::class)->findBy(['client' => $user]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Compte introuvable'

            ], 203);
        }
        $lAbonnementCollections = $this->paginator->paginate($abonnement, $page, $this->myFunction::PAGINATION);
        $lAbonnement = [];
        foreach ($lAbonnementCollections as $abonnement) {

            if ($abonnement->isStatus()) {

                $boutique = $abonnement->getBoutique();
                $lBo = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
                $limgB = [];

                foreach ($lBo  as $bo) {
                    $limgB[]
                        = ['id' => $bo->getId(), 'src' =>  $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
                }
                if (empty($limgB)) {
                    $limgB[]
                        = ['id' => 0, 'src' =>  $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
                }

                if ($boutique->getUser()) {
                    /*   $boutiqueU =  [
                        'codeBoutique' => $boutique->getCodeBoutique(),
                        'nombre_produit' => count($boutique->getProduits()),
                        'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                        'description' => $boutique->getDescription() ?? "Aucune",
                        'titre' => $boutique->getTitre() ?? "Aucun",
                        'status' => $boutique->isStatus(),
                        'note' => $this->myFunction->noteBoutique($boutique->getId()),

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
                        // 'produits' => $listProduit,


                    ]; */

                    $boutiqueU =    $this->myFunction->boutiqueByIdModel($boutique->getId(), $user);
                    array_push($lAbonnement, $boutiqueU);
                }
            }
        }
        return new JsonResponse([
            'data' => $lAbonnement

        ], 200);
    }


    /**
     * @Route("/abonnement/boutique", name="AbonnementReadBoutique", methods={"GET"})
     *  @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     */
    public function AbonnementReadBoutique(Request $request,)
    {
        // $keySecret = $request->query->get('keySecret');
        $codeBoutique = $request->query->get('codeBoutique');
        $page         = $request->query->get('page') == null || $request->query->get('page') == 0 ? 1 : $request->query->get('page');


        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);
        if (!$boutique) {
            return new JsonResponse([
                'message' => 'Compte introuvable'

            ], 203);
        }
        $abonnement = $this->em->getRepository(AbonnementBoutique::class)->findBy(['boutique' => $boutique]);
        $lAbonnementCollections = $this->paginator->paginate($abonnement, $page, $this->myFunction::PAGINATION);
        $l_Abonnes = [];
        foreach ($lAbonnementCollections as $abonnement) {

            if ($abonnement->isStatus()) {


                $user = $abonnement->getClient();
                $userU = [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                    'status' => $user->isStatus(),
                    'typeUser' => $user->getTypeUser()->getId(),
                    'dateCreated' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                    // 'nom' => $user->getNom()
                ];
                array_push($l_Abonnes, $userU);
            }
        }
        return new JsonResponse([
            'data' => $l_Abonnes

        ], 200);
    }


    /**
     * @Route("/abonnement/produit/read", name="AbonnementProduitRead", methods={"GET"})
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
    public function AbonnementProduitRead(Request $request,)
    {
        $page =
            $request->get('page') ?? 1;
        $client = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);


        $abonnementS = $this->em->getRepository(AbonnementBoutique::class)->findBy(['client' => $client, 'status' => 1]);
        $nomnre = count($abonnementS);
        $lP = [];
        foreach ($abonnementS as $abonnement) {
            $boutique = $abonnement->getBoutique();
            $produits = $boutique->getProduits();
            $limit = 50 /   $nomnre;

            $lProduit = $this->paginator->paginate($produits, $page, $limit);


            foreach ($lProduit as $produit) {
                # code... 

                if ($produit->isStatus() && $produit->getQuantite() > 0) {



                    $produitF =
                        $this->myFunction->ProduitModel($produit, $client);

                    array_push($lP, $produitF);
                }
            }
        }
        return
            new JsonResponse(
                [
                    'data'
                    => $lP,

                    'statusCode' => 200

                ],
                200
            );
    }
}
