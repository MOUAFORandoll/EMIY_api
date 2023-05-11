<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Billet;
use App\Entity\ListProduitPromotion;
use App\Entity\ModePaiement;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\UserPlateform;

class PromotionController extends AbstractController
{


    private $em;
    private   $serializer;
    private $mailer;
    private $client;
    private $jwt;
    private $jwtRefresh;
    private $validator;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,

        HttpClientInterface $client,
        JWTTokenManagerInterface $jwt,
        RefreshTokenManagerInterface $jwtRefresh,
        ValidatorInterface $validator
    ) {
        $this->em = $em;
        $this->serializer = $serializer;

        $this->client = $client;
        $this->jwt = $jwt;
        $this->jwtRefresh = $jwtRefresh;
        $this->validator = $validator;
    }

    public function getUniqueTransactionId()
    {


        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 7; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistTransaction = $this->em->getRepository(Transaction::class)->findOneBy(['token' => $chaine]);
        if ($ExistTransaction) {
            return
                $this->getUniqueTransactionId();
        } else {
            return $chaine;
        }
    }

    /**
     * @Route("/promotion/produit/new", name="promtion", methods={"POST"})
     * @param array $data doit contenir la keySecret  de l'utilsateur a modifier, le typeUser a affecter
     * @param Request $request
     * @return JsonResponse
     */
    public function promtion(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['codeProduit']) || empty($data['prixPromotion']) || empty($data['idPromotion']) || empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer   '
            ], 400);
        }
        $produit = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $data['codeProduit']]);
        if (!$produit) {
            return new JsonResponse([
                'message' => 'produit introuvable '

            ], 203);
        }

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        $promotion = $this->em->getRepository(Promotion::class)->findOneBy(['id' => $data['idPromotion']]);

        if ($user == $produit->getBoutique()->getUser() && $promotion) {

            $listpp = new ListProduitPromotion();

            $listpp->setProduit($produit);
            $listpp->setPromotion($promotion);
            $listpp->setPrixPromotion($data['prixPromotion']);

            $this->em->persist($listpp);

            $this->em->flush();

            return new JsonResponse([
                'message' => 'Promotion ajoute.',


            ], 200);
        } else {
            return new JsonResponse([
                'message' => 'Une erreur est survenue'

            ], 203);
        }
    }
}
