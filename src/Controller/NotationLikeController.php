<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Billet;
use App\Entity\Boutique;
use App\Entity\ListProduitPromotion;
use App\Entity\ModePaiement;
use App\Entity\NotationBoutique;
use App\Entity\LikeProduit;
use App\Entity\Place;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Promotion;
use App\Entity\Transaction;
use App\Entity\TypeUser;
use App\FunctionU\MyFunction;
use DateTime;
use FFI\Exception;
use SebastianBergmann\Type\TrueType;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Entity\UserPlateform;

class NotationLikeController  extends AbstractController
{

    private $myFunction;

    private $em;
    public function __construct(

        EntityManagerInterface $em,
        MyFunction  $myFunction,

    ) {
        $this->myFunction = $myFunction;
        $this->em = $em;
    }


    /**
     * @Route("/like/produit", name="LikeProduitNew", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function LikeProduitNew(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['codeProduit'])  || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer'
            ], 400);
        }
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $data['codeProduit']]);
        if (!$produit) {
            return new JsonResponse([
                'message' => 'produit introuvable '

            ], 203);
        }


        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if ($user) {

            $existNote = $this->em->getRepository(LikeProduit::class)->findOneBy(['produit' => $produit, 'client' => $user]);
            if ($existNote) {

                $existNote->setLike_produit(!$existNote->isLike_produit());
                $this->em->persist($existNote);
            } else {
                $note = new LikeProduit();

                $note->setProduit($produit);
                $note->setClient($user);
                // $note->setLike_produit(1);
                $this->em->persist($note);
            }




            $this->em->flush();
            $lsImgP    = [];
            $lProduitO = $this->em->getRepository(ProduitObject::class)->findBy(['produit' => $produit]);
            foreach ($lProduitO as $produit0) {
                $lsImgP[]
                    = ['id' => $produit0->getId(), 'src' => $this->myFunction::BACK_END_URL . '/images/produits/' . $produit0->getSrc()];
            }
            $produitU = [

                'id' => $produit->getId(),
                'like' => $this->myFunction->isLike_produit($produit->getId()),
                'islike' =>   $user == null ? false : $this->myFunction->userlikeProduit($produit->getId(), $user),
                'codeProduit' => $produit->getCodeProduit(),
                'boutique' => $produit->getBoutique()->getTitre(),
                'description' => $produit->getDescription(),
                'titre' => $produit->getTitre(),
                'negociable' => $produit->isNegociable(), 'date ' => date_format($produit->getDateCreated(), 'Y-m-d H:i'),
                'quantite' => $produit->getQuantite(),
                'prix' => $produit->getPrixUnitaire(),
                'status' => $produit->isStatus(),
                // 'promotion' => $produit->getListProduitPromotions()  ? end($produit->getListProduitPromotions())->getPrixPromotion() : 0,
                'images' => $lsImgP

            ];
            return new JsonResponse([
                'message' => 'Like ajoute.',
                'produit' =>  $produitU

            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue'

            ], 203);
        }
    }


    /**
     * @Route("/like/produit", name="LikeUserRead", methods={"GET"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function LikeUserRead(Request $request)
    {




        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecret')]);

        if (!$user) {
            return new JsonResponse(
                [
                    'message' => 'Desolez l\'utilisateur en question a des contraintes',

                ],
                203
            );
        }
        $likeList = $this->em->getRepository(LikeProduit::class)->findBy(['client' => $user, 'like_produit' => 1]);

        $lP = [];
        foreach ($likeList  as $like) {


            $produit = $like->getProduit();
            $produitF =
                $this->myFunction->ProduitModel($produit, $user);

            array_push($lP, $produitF);
        }
        return new JsonResponse([

            'data' =>
            $lP

        ], 200);
    }




    /**
     * @Route("/notation/boutique", name="NotationBoutiqueNew", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function NotationBoutiqueNew(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['codeBoutique']) || empty($data['note']) || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer'
            ], 400);
        }
        $boutique = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $data['codeBoutique']]);
        if (!$boutique) {
            return new JsonResponse([
                'message' => 'boutique introuvable '

            ], 203);
        }
        if ($data['note'] < 0 || $data['note'] > 5) {
            return new JsonResponse([
                'message' => 'UNe erreur est survenue'

            ], 203);
        }

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if ($user) {

            $existNote = $this->em->getRepository(NotationBoutique::class)->findOneBy(['boutique' => $boutique, 'client' => $user]);
            if ($existNote) {

                $existNote->setNote($data['note']);
                $this->em->persist($existNote);
            } else {
                $note = new NotationBoutique();

                $note->setBoutique($boutique);
                $note->setClient($user);
                $note->setNote($data['note']);
                $this->em->persist($note);
            }




            $this->em->flush();

            return new JsonResponse([
                'message' => 'Note ajoute.',


            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue'

            ], 203);
        }
    }
}
