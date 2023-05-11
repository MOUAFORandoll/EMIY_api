<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Billet;
use App\Entity\Boutique;
use App\Entity\ListProduitPromotion;
use App\Entity\ModePaiement;
use App\Entity\NotationBoutique;
use App\Entity\NotationProduit;
use App\Entity\Place;
use App\Entity\Produit;
use App\Entity\Promotion;
use App\Entity\Transaction;
use App\Entity\TypeUser;
use DateTime;
use FFI\Exception;
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

class NotationController  extends AbstractController
{


    private $em;
    public function __construct(

        EntityManagerInterface $em,

    ) {
        $this->em = $em;
    }


    /**
     * @Route("/notation/produit", name="NotationProduitNew", methods={"POST"})
      
     * @param Request $request
     * @return JsonResponse
     */
    public function NotationProduitNew(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['codeProduit']) || empty($data['note']) || empty($data['keySecret'])) {
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
        if ($data['note'] < 0 || $data['note'] > 5) {
            return new JsonResponse([
                'message' => 'UNe erreur est survenue'

            ], 203);
        }

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);


        if ($user) {

            $existNote = $this->em->getRepository(NotationProduit::class)->findOneBy(['produit' => $produit, 'client' => $user]);
            if ($existNote) {

                $existNote->setNote($data['note']);
                $this->em->persist($existNote);
            } else {
                $note = new NotationProduit();

                $note->setProduit($produit);
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
