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
        $this->em->beginTransaction();
        try {
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
            if (!$user || $boutique) {
                return new JsonResponse([
                    'message' => 'Compte introuvable'

                ], 203);
            }
            $abonnementExist = $this->em->getRepository(AbonnementBoutique::class)->findOneBy(['boutique' => $boutique]);
            if ($abonnementExist) {

                $abonnementExist->setStatus(!$abonnementExist->isStatus());
            } else {
                $abonnement = new AbonnementBoutique();

                $abonnement->setClient($user);
                $abonnement->setBoutique($boutique);
            }
            $this->em->persist($abonnement);
            $this->em->flush();
            return new JsonResponse([
                'message' => 'Success',
                'status' => true,
                'id' =>  $boutique->getId()

            ], 200);
        } catch (\Exception $e) {
            // Une erreur s'est produite, annulez la transaction
            $this->em->rollback();
            return new JsonResponse([
                'message' => 'Une erreur est survenue'
            ], 203);
        }
    }

    /**
     * @Route("/abonnement", name="AbonnementReadClient", methods={"GET"})
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
        $page = $request->query->get('page');

        $this->em->beginTransaction();
        try {
            $data
                =        $data = $request->toArray();

            // return new JsonResponse([

            //     'd' => $data
            // ], 400);
            if (empty($data['codeBoutique'])   || empty($data['keySecret'])) {
                return new JsonResponse([
                    'message' => 'Veuillez recharger la page et reessayer   ',

                ], 400);
            }

            $codeBoutique = $data['codeBoutique'];

            $keySecret = $data['keySecret'];

            $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
            $abonnement = $this->em->getRepository(AbonnementBoutique::class)->findBy(['client' => $user]);
            if (!$user) {
                return new JsonResponse([
                    'message' => 'Compte introuvable'

                ], 203);
            }
            $lAbonnementCollections = $this->paginator->paginate($abonnement, $page, 12);
            $lAbonnement = [];
            foreach ($lAbonnementCollections as $abonnement) {

                if ($abonnement->isStatus()) {



                    $abonnementU =  [
                        'id' => $abonnement->getId(),
                        'boutique_id' => $abonnement->getBoutique()->getId(),
                        'boutique_title' => $abonnement->getBoutique()->getTitre(),

                        'date' =>  $abonnement->getDateCreated()->format('Y-m-d H:i'),
                        //  'status' => $abonnement->isStatus(),


                    ];
                    array_push($lAbonnement, $abonnementU);
                }
            }
            return new JsonResponse([
                'data' => $lAbonnement

            ], 200);
        } catch (\Exception $e) {
            // Une erreur s'est produite, annulez la transaction
            $this->em->rollback();
            return new JsonResponse([
                'message' => 'Une erreur est survenue'
            ], 203);
        }
    }


    /**
     * @Route("/abonnement", name="AbonnementReadBoutique", methods={"GET"})
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
        $page = $request->query->get('page');

        $this->em->beginTransaction();
        try {

            // if (empty($keySecret)) {
            //     return new JsonResponse([
            //         'message' => 'Veuillez recharger la page et reessayer   ',

            //     ], 400);
            // }
            $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);
            if (!$boutique) {
                return new JsonResponse([
                    'message' => 'Compte introuvable'

                ], 203);
            }
            $abonnement = $this->em->getRepository(AbonnementBoutique::class)->findBy(['boutique' => $boutique]);
            $lAbonnementCollections = $this->paginator->paginate($abonnement, $page, 12);
            $lAbonnement = [];
            foreach ($lAbonnementCollections as $abonnement) {

                if ($abonnement->isStatus()) {



                    $abonnementU =  [
                        'id' => $abonnement->getId(),
                        'cient_id' => $abonnement->getClient()->getId(),
                        'client_name' => $abonnement->getClient()->getNom() + ' ' + $abonnement->getClient()->getPrenom(),
                        'client_src' => $abonnement->getClient()->getTitre(),

                        'date' =>  $abonnement->getDateCreated()->format('Y-m-d H:i'),
                        // 'status' => $abonnement->isStatus(),


                    ];
                    array_push($lAbonnement, $abonnementU);
                }
            }
            return new JsonResponse([
                'data' => $lAbonnement

            ], 200);
        } catch (\Exception $e) {
            // Une erreur s'est produite, annulez la transaction
            $this->em->rollback();
            return new JsonResponse([
                'message' => 'Une erreur est survenue'
            ], 203);
        }
    }
}
