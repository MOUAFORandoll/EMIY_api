<?php

namespace App\Controller;

use App\Const\ApiUrl;
use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\ShortLike;
use App\Entity\ShortObject;
use App\Entity\Short;
use App\Entity\ShortComment;
use App\Entity\ShortCommentLike;
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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\UserReadShort;
use App\Entity\ListProduitShort;
use App\Entity\Produit;

class ShortController extends AbstractController
{
    private $em;
    private   $serializer;
    private $paginator;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction,

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->paginator = $paginator;

        $this->clientWeb = $clientWeb;
    }

    /**
     * @Route("/short/foryou/read", name="ShortForYouRead", methods={"GET"})
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
    public function ShortForYouRead(Request $request)
    {


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $page =
            $request->get('page') ?? 1;

        $lShortF = [];

        $result =
            $this->filterShortForUserRead($this->em->getRepository(Short::class)->findAll(),  $user);
        $lShort = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        foreach ($lShort as $short) {


            // $short->setCodeShort($this->myFunction->getUniqueNameShort());
            // $this->em->persist($short);
            // $this->em->flush();



            $boutique = $short->getBoutique();

            if ($boutique) {
                if ($boutique->isStatus()) {

                    $shortF =     $this->myFunction->ShortModel($short, $user);
                    array_push($lShortF, $shortF);
                }
            }
        }


        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,
                ],
                200
            );
    }


    /**
     * @Route("/short/suivis/read", name="ShortSuivisRead", methods={"GET"})
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
    public function ShortSuivisRead(Request $request)
    {


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $page =
            $request->get('page') ?? 1;

        $lShortF = [];




        $shortSuivis =
            $this->em->getRepository(Short::class)->findShortsForSubscribedBoutiques($user);
        $result =
            $this->filterShortForUserRead($shortSuivis,  $user);
        $lShort = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        foreach ($lShort as $short) {


            // $short->setCodeShort($this->myFunction->getUniqueNameShort());
            // $this->em->persist($short);
            // $this->em->flush();



            $boutique = $short->getBoutique();

            if ($boutique) {
                if ($boutique->isStatus()) {

                    $lShortF[] =     $this->myFunction->ShortModel($short, $user);
                }
            }
        }


        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,
                ],
                200
            );
    }

    public function filterShortForUserRead($shortList, $user)
    {
        // Créer deux tableaux pour stocker les vidéos lues et non lues
        $unreadShorts = [];
        $readShorts = [];

        // Parcourir la liste des vidéos
        foreach ($shortList as $short) {
            // Vérifier si l'utilisateur a déjà consulté la vidéo
            $exist = $this->em->getRepository(UserReadShort::class)->findOneBy(['short' => $short, 'client' => $user]);

            // Si la vidéo est marquée comme lue par l'utilisateur
            if ($exist) {
                // Ajouter la vidéo dans le tableau des vidéos lues
                $readShorts[] = $short;
            } else {
                // Sinon, ajouter la vidéo dans le tableau des vidéos non lues
                $unreadShorts[] = $short;
            }
        }

        // Fusionner les tableaux des vidéos non lues et des vidéos lues pour obtenir la liste triée
        $sortedShortList = array_merge($unreadShorts, $readShorts);

        return $sortedShortList;
    }


    /**
     * @Route("/short/read/unique", name="ShortReadUnique", methods={"GET"})
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
    public function ShortReadUnique(Request $request)
    {


        $boutiqueU = [];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $short =     ($request->get('id')  != 'null')   ?  $this->em->getRepository(Short::class)->findOneBy(['id' => $request->get('id')]) : $this->em->getRepository(Short::class)->findOneBy(['codeShort' => $request->get('codeShort')]);
        if (!$short) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }
        $boutique = $short->getBoutique();


        $shortF =     $this->myFunction->ShortModel($short, $user);


        return
            new JsonResponse(
                [
                    'data'
                    =>  $shortF,
                ],
                200
            );
    }



    /**
     * @Route("/like/short", name="LikeShortNew", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function LikeShortNew(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['id'])  || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer'
            ], 400);
        }
        $short = $this->em->getRepository(Short::class)->findOneBy(['id' => $data['id']]);
        if (!$short) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if ($user) {

            $existLikeShort = $this->em->getRepository(ShortLike::class)->findOneBy(['short' => $short, 'client' => $user]);
            if ($existLikeShort) {

                $existLikeShort->setLike_short(!$existLikeShort->isLike_short());
                $this->em->persist($existLikeShort);
                $data = [
                    'title' => 'Je like un Short',
                    'description' => 'Je like un Short',
                    'user' => $user,
                    'sujet' =>  $existLikeShort,
                ];
                $notification =   $this->myFunction->createNotification(2, $data);
                $notificationEmit =   $this->myFunction->modelNotification($notification);
                $this->myFunction->Socekt_Emit('notifications', $notificationEmit);
            } else {
                $likeShort = new ShortLike();

                $likeShort->setShort($short);
                $likeShort->setClient($user);
                // $likeShort->setLike_short(1);
                $this->em->persist($likeShort);
                $data = [
                    'title' => 'Je like un Short',
                    'description' => 'Je like un Short',
                    'user' => $user,
                    'sujet' =>  $likeShort,
                ];
                $notification =   $this->myFunction->createNotification(2, $data);
                $notificationEmit =   $this->myFunction->modelNotification($notification);
                $this->myFunction->Socekt_Emit('notifications', $notificationEmit);
            }




            $this->em->flush();
            $boutique = $short->getBoutique();

            if (!$boutique) {

                return new JsonResponse([
                    'message' => 'Une erreur est survenue'

                ], 203);
            }




            if (!$boutique->isStatus()) {
                return new JsonResponse([
                    'message' => 'Like ajoute.',
                    'short' =>  []

                ], 203);
            }

            $lBo   = $this->em->getRepository(BoutiqueObject::class)->findBy(['boutique' => $boutique]);
            $limgB = [];

            foreach ($lBo as $bo) {
                $limgB[]
                    = ['id' => $bo->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/boutiques/' . $bo->getSrc()];
            }
            if (empty($limgB)) {
                $limgB[]
                    = ['id' => 0, 'src' => $this->myFunction::BACK_END_URL . '/images/default/boutique.png'];
            }
            $boutiqueU = [
                'codeBoutique' => $boutique->getCodeBoutique(),
                'user' => $boutique->getUser()->getNom() . ' ' . $boutique->getUser()->getPrenom(),
                'description' => $boutique->getDescription() ?? "Aucune",
                'titre' => $boutique->getTitre() ?? "Aucun",
                'status' => $boutique->isStatus(),
                'note' => $this->myFunction->noteBoutique($boutique->getId()),

                'dateCreated' => date_format($boutique->getDateCreated(), 'Y-m-d H:i'),
                'images' => $limgB,
                'localisation' => $boutique->getLocalisation() ? [
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

            $shortF = [

                'id' => $short->getId(),
                'titre' => $short->getTitre() ?? "Aucun",
                'description' => $short->getDescription() ?? "Aucun",
                'status' => $short->isStatus(),
                'Preview' => $short->getPreview(),
                'src' => $short->getSrc(),
                'is_like' => $user == null ? false : $this->myFunction->userlikeShort($short, $user),
                'nbre_like' => count($this->myFunction->ListLikeShort($short)),
                'codeShort' =>
                $short->getCodeShort(),     'nbre_commentaire' => count($this->myFunction->ListCommentShort($short)),
                'date' =>
                date_format($short->getDateCreated(), 'Y-m-d H:i'),
                'boutique' => $boutiqueU
            ];

            return new JsonResponse([
                'message' => 'Like ajoute.',
                'short' =>  $shortF

            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue'

            ], 203);
        }
    }



    /**
     * @Route("/comment/short", name="CommentShortNew", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function CommentShortNew(Request $request)
    {
        $data = $request->toArray();
        if ((empty($data['id']) && empty($data['idRef'])) || empty($data['keySecret']) || empty($data['comment'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer'
            ], 400);
        }


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }
        $comm = $data['comment'];



        $comment = new ShortComment();

        if (!empty($data['id']) && empty($data['idRef'])) {
            $short = $this->em->getRepository(Short::class)->findOneBy(['id' => $data['id']]);

            $comment->setShort($short);
        }
        if (empty($data['id']) && !empty($data['idRef'])) {

            $commentRef = $this->em->getRepository(ShortComment::class)->findOneBy(['id' => $data['idRef']]);

            $comment->setReferenceCommentaire($commentRef);
        }



        $comment->setClient($user);
        $comment->setComment($comm);
        $this->em->persist($comment);


        $this->em->flush();

        $profile      = count($comment->getClient()->getUserObjects())  == 0 ? '' :  $comment->getClient()->getUserObjects()->first()->getSrc();

        $commentaire =  [

            'id' => $comment->getId(), 'date' =>
            date_format($comment->getDateCreated(), 'Y-m-d H:i'),
            'commentaire' => $comment->getComment() ?? "Aucun",
            'username' => $comment->getClient()->getNom() . ' ' . $comment->getClient()->getPreNom() ?? "Aucun",
            'userphoto' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,
            'nbre_com' => count($this->myFunction->ListCommentComment($comment)),
            'sub_responses' => false,
            'target_user' => '', 'nbre_like_com' => count($this->myFunction->ListLikeCommentShort($comment)),
            'is_like_com' =>   $user == null ? false : $this->myFunction->userlikeShortCom($comment, $user),


        ];
        $data = [
            'title' => 'Je commente un Short',
            'description' => 'Je commente un Short',
            'user' => $user,
            'sujet' =>  $comment,
        ];
        $notification =   $this->myFunction->createNotification(3, $data);
        $notificationEmit =   $this->myFunction->modelNotification($notification);


        $this->myFunction->Socekt_Emit('notifications', $notificationEmit);
        return new JsonResponse([
            'message' => 'Commentaire ajoute.',
            'commentaire' =>  $commentaire

        ], 200);
    }



    /**
     * @Route("/like/comment", name="LikeComment", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function LikeComment(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['id'])  || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer'
            ], 400);
        }
        $com = $this->em->getRepository(ShortComment::class)->findOneBy(['id' => $data['id']]);
        if (!$com) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if ($user) {

            $existLikeShortCom = $this->em->getRepository(ShortCommentLike::class)->findOneBy(['shortComment' => $com, 'client' => $user]);
            if ($existLikeShortCom) {

                $existLikeShortCom->setLike_comment(!$existLikeShortCom->isLike_comment());
                $this->em->persist($existLikeShortCom);
                $data = [
                    'title' => 'Je commente un Short',
                    'description' => 'Je commente un Short',
                    'user' => $user,
                    'sujet' =>  $existLikeShortCom,
                ];
                $notification =   $this->myFunction->createNotification(4, $data);
                $notificationEmit =   $this->myFunction->modelNotification($notification);
                $this->myFunction->Socekt_Emit('notifications', $notificationEmit);
            } else {
                $likeShortCom = new ShortCommentLike();

                $likeShortCom->setShortComment($com);
                $likeShortCom->setClient($user);
                $this->em->persist($likeShortCom);
                $data = [
                    'title' => 'Je commente un Short',
                    'description' => 'Je commente un Short',
                    'user' => $user,
                    'sujet' =>  $likeShortCom,
                ];
                $notification =   $this->myFunction->createNotification(4, $data);
                $notificationEmit =   $this->myFunction->modelNotification($notification);
                $this->myFunction->Socekt_Emit('notifications', $notificationEmit);
            }




            $this->em->flush();


            $profile      = count($com->getClient()->getUserObjects())  == 0 ? '' :  $com->getClient()->getUserObjects()->first()->getSrc();



            $commentaire =  [

                'id' => $com->getId(),
                'date' =>
                date_format($com->getDateCreated(), 'Y-m-d H:i'),

                'commentaire' => $com->getComment() ?? "Aucun",
                'username' => $com->getClient()->getNom() . ' ' . $com->getClient()->getPreNom() ?? "Aucun",
                'userphoto' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,
                'nbre_com' => count($this->myFunction->ListCommentComment($com)),
                'sub_responses' => false,
                'target_user' => '', 'nbre_like_com' => count($this->myFunction->ListLikeCommentShort($com)),
                'is_like_com' =>   $user == null ? false : $this->myFunction->userlikeShortCom($com, $user),

            ];

            return new JsonResponse([
                'message' => 'Like ajoute.',
                'commentaire' =>  $commentaire

            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue'

            ], 203);
        }
    }


    /**
     * @Route("/comment/short/", name="CommentShortList", methods={"GET"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function CommentShortList(Request $request)
    {


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $idShort =
            $request->get('idShort');
        $short = $this->em->getRepository(Short::class)->findOneBy(['id' => $idShort]);
        if (!$short) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }
        $commentList = $this->em->getRepository(ShortComment::class)->findBy(['short' => $short]);
        $listCommentaires = [];
        foreach ($commentList as $comm) {


            $profile      = count($comm->getClient()->getUserObjects())  == 0 ? '' :  $comm->getClient()->getUserObjects()->first()->getSrc();

            $commentaire =  [

                'id' => $comm->getId(),
                'date' =>
                date_format($comm->getDateCreated(), 'Y-m-d H:i'),   'commentaire' => $comm->getComment() ?? "Aucun",
                'username' => $comm->getClient()->getNom() . ' ' . $comm->getClient()->getPreNom() ?? "Aucun",
                'userphoto' =>   $this->myFunction::BACK_END_URL . '/images/users/' . $profile,
                'nbre_com' => count($this->myFunction->ListCommentComment($comm)),
                'sub_responses' => false,
                'target_user' => '', 'nbre_like_com' => count($this->myFunction->ListLikeCommentShort($comm)),
                'is_like_com' =>   $user == null ? false : $this->myFunction->userlikeShortCom($comm, $user),

            ];
            $listCommentaires[] =  $commentaire;
        }

        return
            new JsonResponse(
                [
                    'data'
                    => array_reverse($listCommentaires),
                ],
                200
            );
    }



    /**
     * @Route("/comment/comment/short/", name="CommentCommentShortList", methods={"GET"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function CommentCommentShortList(Request $request)
    {


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $idRef =
            $request->get('idRef');
        $Commmentaire = $this->em->getRepository(ShortComment::class)->findOneBy(['id' => $idRef]);
        if (!$Commmentaire) {
            return new JsonResponse([
                'message' => 'short introuvable '

            ], 203);
        }
        $commentList = $this->em->getRepository(ShortComment::class)->findBy(['reference_commentaire' => $Commmentaire]);
        $listCommentaires = [];
        foreach ($commentList as $comm) {





            $profile      = count($comm->getClient()->getUserObjects())  == 0 ? '' :  $comm->getClient()->getUserObjects()->first()->getSrc();

            $commentaire =  [

                'id' => $comm->getId(),
                'date' =>
                date_format($Commmentaire->getDateCreated(), 'Y-m-d H:i'),   'commentaire' => $comm->getComment() ?? "Aucun",
                'username' => $comm->getClient()->getNom() . ' ' . $comm->getClient()->getPreNom() ?? "Aucun",
                'userphoto' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,
                'nbre_com' =>  0, //count( $this->myFunction->ListCommentComment($comm)),
                'sub_responses' => false,
                'target_user' => '',
                'nbre_like_com' => count($this->myFunction->ListLikeCommentShort($comm)),
                'is_like_com' =>   $user == null ? false : $this->myFunction->userlikeShortCom($comm, $user),

            ];
            $listCommentaires[] =  $commentaire;
            $listCommentaires = array_merge($listCommentaires, $this->getSubComment($comm, $user));
        }

        usort($listCommentaires, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return
            new JsonResponse(
                [
                    'data'
                    => /* array_reverse */ ($listCommentaires),
                ],
                200
            );
    }
    // Fonction de comparaison pour trier les objets par leur attribut DateTime
    function compareByDateTime($a, $b)
    {
        return $a['date'] <=> $b['date'];
    }

    public function   getSubComment($commming, $user)
    {
        $listCommentaires = [];

        $commentList = $this->em->getRepository(ShortComment::class)->findBy(['reference_commentaire' => $commming]);
        foreach ($commentList as $comm) {
            $profile      = count($comm->getClient()->getUserObjects())  == 0 ? '' :  $comm->getClient()->getUserObjects()->first()->getSrc();

            $commentaire =  [

                'id' => $comm->getId(),
                'date' =>
                date_format($comm->getDateCreated(), 'Y-m-d H:i'),   'commentaire' => $comm->getComment() ?? "Aucun",
                'username' => $comm->getClient()->getNom(),
                'userphoto' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,
                'nbre_com' => /* count( $this->myFunction->ListCommentComment($comm)) */ 0,
                'nbre_like_com' => count($this->myFunction->ListLikeCommentShort($comm)),
                'is_like_com' =>   $user == null ? false : $this->myFunction->userlikeShortCom($comm, $user),
                'sub_responses' => true,
                'target_user' => $commming->getClient()->getNom(),
            ];
            $listCommentaires[] = $commentaire;
            $listCommentaires = array_merge($listCommentaires, $this->getSubComment($comm, $user));
        }
        return $listCommentaires;
    }
    /**
     * @Route("/comment/delete", name="Dell", methods={"GET"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function Dell(Request $request)
    {


        $short = $this->em->getRepository(ShortComment::class)->findOneBy(['id' => 3]);

        $this->em->remove($short);
        $this->em->flush();
        return
            new JsonResponse(
                [
                    'data'
                    => '',
                ],
                200
            );
    }

    /**
     * @Route("/short/user/read", name="UserReadShort", methods={"GET"})
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
    public function UserReadShort(Request $request,)
    {

        $short = $this->em->getRepository(Short::class)->findOneBy(['codeShort' => $request->get('codeShort')]);
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' =>  $request->get('keySecret')]);

        // dd($file);

        if (!$short ||   !$user) {

            return new JsonResponse(
                [
                    'message' => 'Verifier votre requette',
                    // 'data'=> $data
                ],
                400
            );
        }
        $exist = $this->em->getRepository(UserReadShort::class)->findOneBy(['short' =>  $short, 'client' => $user]);
        if ($exist) {
            $exist->setDateCreated(new \DateTime());
            $this->em->persist($exist);
            $this->em->flush();

            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success'
                    ],
                    200
                );
        }

        $read = new UserReadShort();
        $read->setClient($user);
        $read->setShort($short);
        $this->em->persist($read);
        $this->em->flush();

        return
            new JsonResponse(
                [
                    'message'
                    =>  'success'
                ],
                200
            );
    }

    /**
     * @Route("/short/for/boutique/read", name="ShortBoutiqueReadFor", methods={"GET"})
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
    public function ShortBoutiqueReadFor(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);





        $lShortF = [];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        $page =
            $request->get('page');

        // $keySecret = $data['keySecret'];
        $codeBoutique =
            $request->get('codeBoutique');
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);

        $result = $this->em->getRepository(Short::class)->findBy(['boutique' => $boutique]);
        $lShort = $this->paginator->paginate($result, $page, $this->myFunction::PAGINATION);
        foreach ($lShort as $short) {



            $shortF =     $this->myFunction->ShortModel($short, $user);

            array_push($lShortF, $shortF);
        }

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,
                ],
                200
            );
    }
    /**
     * @Route("/short/boutique/read", name="ShortBoutiqueRead", methods={"GET"})
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
    public function ShortBoutiqueRead(Request $request)
    {

        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;



        $lShortF = [];


        // $keySecret = $data['keySecret'];
        $codeBoutique =
            $request->get('codeBoutique');
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $codeBoutique]);
        $user         = $boutique->getUser();
        $lShort = $this->em->getRepository(Short::class)->findBy(['boutique' => $boutique]);
        foreach ($lShort as $short) {




            $shortF =     $this->myFunction->ShortModel($short, $user);

            $lShortF[] =  $shortF;
        }

        return
            new JsonResponse(
                [
                    'data'
                    =>  $lShortF,
                ],
                200
            );
    }
    /**
     * @Route("/short/new", name="ShortNew", methods={"POST"})
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
    public function ShortNew(Request $request, SluggerInterface $slugger)
    {

        // try {
        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;




        $data = [

            'titre' => $request->get('titre'),
            'description' => $request->get('description'),

            'codeBoutique' => $request->get('codeBoutique'),
            'produits' => $request->get('produits'),
        ];

        if (
            empty($data['titre']) || empty($data['description'])


            || empty($data['codeBoutique'])
        ) {
            return new JsonResponse(
                [
                    'message' => 'Verifier votre requette',
                    // 'data'=> $data
                ],
                400
            );
        }

        $titre = $data['titre'];
        $description = $data['description'];
        $produits =    $data['produits'];
        // dd( $this->getParameter( 'shorts_object' ) );

        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);

        $file = $request->files->get('file');

        // dd($file);

        if (!$file ||   !$boutique) {

            return new JsonResponse(
                [
                    'message' => 'Verifier votre requette',
                    // 'data'=> $data
                ],
                400
            );
        }

        $nameFile
            = $this->myFunction->getUniqueNameShort();
        $codeShort                 = $nameFile;
        $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilenameData = $slugger->slug($originalFilenameData);
        $newFilenameData =
            $nameFile . '.' . $file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {

            $file->move(
                $this->getParameter('shorts_object'),
                $newFilenameData
            );
            $this->extractImageFromVideoAction(
                $this->getParameter('shorts_object') . '/' .
                    $newFilenameData,

                $this->getParameter('shorts_object') . '/' .    $nameFile . '.jpg'
            );
            $short = new Short();
            $short->setSrc($newFilenameData);
            $short->setPreview($nameFile . '.jpg');
            $short->setTitre($titre);
            $short->setCodeShort($codeShort);
            $short->setDescription(
                $description
            );
            $short->setBoutique($boutique);
            $this->em->persist($short);
            $produits = explode(',', $produits);
            if (!empty($produits)) {
                foreach ($produits  as $id) {
                    $produit = $this->em->getRepository(Produit::class)->findOneBy(['id' => $id]);

                    $produit_short = new ListProduitShort();
                    $produit_short->setShort($short);
                    $produit_short->setProduit($produit);

                    $this->em->persist($produit_short);
                }
            }
            $this->em->flush();

            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success'
                    ],
                    200
                );
        } catch (FileException $e) {
            return
                new JsonResponse([

                    'status' =>   false,
                    'message' =>   'Vos fichiers ne sont pas valides'

                ], 203);
        }

        // } catch (\Exception $e) {
        //     // Une erreur s'est shorte, annulez la transaction

        //     return new JsonResponse([
        //         'message' => 'Une erreur est survenue',
        //         ' $e' => $e
        //     ], 203);
        // }
    }

    public function extractImageFromVideoAction($videoPath, $imagePath)
    {

        // Exécute la commande FFmpeg
        $command = "ffmpeg -i $videoPath -ss 00:00:01 -vframes 1 $imagePath";
        exec($command);

        // Vérifie si l'image a été correctement extraite
        if (file_exists($imagePath)) {
            // Retourne l'image extraite en tant que réponse
            return new Response(file_get_contents($imagePath), 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="image.jpg"'
            ]);
        } else {
            // Gère l'erreur si l'image n'a pas pu être extraite
            return new Response('Erreur lors de l\'extraction de l\'image', 500);
        }
    }
}
/****
 * 
 */
