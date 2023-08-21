<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commission;
use App\Entity\ListProduitShort;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Short;
use App\Entity\TypeCommande;
use App\Entity\TypeNotification;
use App\Entity\TypePaiement;
use App\Entity\TypeTransaction;
use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use Faker\Factory;
use Lcobucci\JWT\Encoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;
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
use App\FunctionU\MyFunction;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Dompdf\Dompdf;

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InitController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }
    /**
     * @Route("/emiy/init/config", name="EmiyInitConfig", methods={"GET"})
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
     * 
     * 
     */
    public function EmiyInitConfig(Request $request)
    {
        $typeU = $this->InitTypeUser();
        $comminssion = $this->InitCommission();
        $typeT = $this->InitTypeTransaction();
        $typeP = $this->InitTypePaiement();
        $typeC = $this->InitTypeCommande();
        $typeM = $this->InitModePaiement();


        return new JsonResponse([
            'type_user' =>
            $typeU,

            'comminssion' =>
            $comminssion,
            'type_transaction' =>
            $typeT, 'type_mode' =>
            $typeM, 'type_comm' =>
            $typeC,
            'type_paiement' =>
            $typeP,

        ], 200);
    }


    /**
     * @Route("/emiy/init", name="EmiyInit", methods={"GET"})
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
     * 
     * 
     */
    public function EmiyInitFakerData(Request $request)
    {


        $boutique = $this->FakerBoutique();
        $produit = $this->FakerProduit();
        $short = $this->FakerShort();
        $point_l = $this->FakerPointLivraison();

        return new JsonResponse([
            'boutique' =>
            $boutique,
            'produit' =>
            $produit, 'short' =>
            $short, 'point_l' =>
            $point_l,

        ], 200);
    }



    public function InitCommission()
    {


        $data = $this->em->getRepository(Commission::class)->findAll();
        if (count($data) >= 1) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        $comminssion = new Commission();

        $comminssion->setPourcentageProduit(2);
        $comminssion->setFraisLivraisonProduit(250);
        $comminssion->setFraisBuyLivreur(500);

        $this->em->persist($comminssion);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function InitTypeUser()
    {


        $t = ['Admin', 'Client', 'Livreur'];

        $data = $this->em->getRepository(TypeUser::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new TypeUser();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function InitTypeNotification()
    {

        $t = ['General', 'Short Like', 'Short Commentaire', 'Short Like Commentaire', 'Message negocaition', 'Message Communication Service client'];
        $data = $this->em->getRepository(TypeNotification::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new TypeNotification();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function InitTypeTransaction()
    {

        $t = ['Achat', 'Retrait', 'Depot'];
        $data = $this->em->getRepository(TypeTransaction::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new TypeTransaction();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }

    public function InitTypePaiement()
    {

        $t = ['Livreur', 'Boutique'];
        $data = $this->em->getRepository(TypePaiement::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new TypePaiement();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function InitTypeCommande()
    {

        $t = ['Commande normale', 'Commande negocie '];

        $data = $this->em->getRepository(TypeCommande::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new TypeCommande();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function InitModePaiement()
    {

        $t = ["Orange Money", 'Momo', 'Carte', 'K-Coin'];

        $data = $this->em->getRepository(ModePaiement::class)->findAll();
        if (
            count($data)

            >= count($t)
        ) {

            return new JsonResponse([
                'message' => 'Exist',


            ], 200);
        }
        for ($i = 0; $i < count($t); $i++) {
            # code...

            $typr = new ModePaiement();

            $typr->setLibelle($t[$i]);

            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }


    /**
     * @Route("/emiy/admin/init", name="EmiyAdminInit", methods={"GET"})
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
     * 
     * 
     */
    public function EmiyAdminInit(Request $request)
    {

        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => 1]);

        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);
        $admin->setTypeUser($typeUser);

        $this->em->persist($admin);

        $this->em->flush();
        return new JsonResponse(
            [
                'message' => 'Success',


            ],
            200
        );
    }



    /**
     * @Route("/emiy/admin/l", name="FakerPointLivraison", methods={"GET"})
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
     * 
     * 
     */
    public function FakerPointLivraison()
    {



        for ($i = 0; $i < 20; $i++) {
            # code...
            $faker = Factory::create();
            $typr = new PointLivraison();

            $typr->setLibelle($faker->name)
                ->setVille(
                    $faker->address
                )

                ->setQuartier($faker->name);
            $this->em->persist($typr);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',


        ], 200);
    }
    public function FakerBoutique()
    {

        $listImageBoutique = [];
        $sourceDirBoutique = 'images/boutiques';
        $filesBoutique  = scandir($sourceDirBoutique);
        foreach ($filesBoutique  as $file) {
            if ($file != '.' && $file != '..')
                $listImageBoutique[] = $file;
        }




        $category = $this->em->getRepository(Category::class)->findAll();
        $user = $this->em->getRepository(UserPlateform::class)->findAll();

        $paragraphs = " Irure elit Lorem incididunt enim pariatur exercitation deserunt sunt consequat quis nisi veniam do. Adipisicing voluptate occaecat id dolore duis incididunt eu occaecat tempor Lorem adipisicing. Sunt officia elit ex anim consectetur
          ";


        for ($i = 0; $i < 10; $i++) {
            # code...
            $faker = Factory::create();

            $localisation = new Localisation();
            $localisation->setVille(
                $faker->address
            );
            $localisation->setLongitude($faker->longitude);
            $localisation->setLatitude($faker->latitude);
            $this->em->persist($localisation);
            $boutique = new Boutique();

            $boutique->setUser($user[random_int(0, count($user) - 1)]);
            $boutique->setDescription($paragraphs);
            $boutique->setTitre($faker->company);
            $boutique->setStatus(true);

            $boutique->setCategory($category[random_int(0, count($category) - 1)]);
            $boutique->setCodeBoutique($this->getUniqueCodeBoutique());



            $boutique->setLocalisation($localisation);

            $this->em->persist($boutique);
            $boutiqueO = new BoutiqueObject();

            $boutiqueO->setSrc($listImageBoutique[random_int(0, count($listImageBoutique) - 1)]);
            $boutiqueO->setBoutique($boutique);
            $this->em->persist($boutiqueO);
        }
        $this->em->flush();

        return
            new JsonResponse(
                [
                    'listImageBoutique '
                    =>
                    $listImageBoutique,
                ],
                200
            );
    }

    public function FakerProduit()
    {
        $listImageProduit = [];
        $sourceDirProduit = 'images/produits';
        $filesProduit = scandir($sourceDirProduit);
        foreach ($filesProduit as $file) {
            if ($file != '.' && $file != '..')
                $listImageProduit[] = $file;
        }

        $paragraphs = " Irure elit Lorem incididunt enim pariatur exercitation deserunt sunt consequat quis nisi veniam do. Adipisicing voluptate occaecat id dolore duis incididunt eu occaecat tempor Lorem adipisicing. Sunt officia elit ex anim consectetur
             proident occaecat sint amet minim et. Sit quis consequat do deserunt sunt. Sunt aliquip esse id aliqua esse esse commodo excepteur ea aliqua labore quis cillum.";

        $lBoutique = $this->em->getRepository(Boutique::class)->findAll();
        $commission = $this->em->getRepository(Commission::class)->findOneBy(['id' => 1]);

        for ($i = 0; $i < 50; $i++) {
            $faker = Factory::create();
            $produit = new Produit();
            $produit->setTitre($faker->company);
            $produit->setDescription($paragraphs);
            $produit->setQuantite(random_int(1, 20));
            $produit->setTaille(random_int(1, 20));
            $price = (random_int(1, 10) * 1000 +
                (random_int(1, 10) * 1000 * $commission->getPourcentageProduit() / 100
                    + $commission->getFraisLivraisonProduit()

                )
            );
            $produit->setPrixUnitaire(random_int(1, 10) * 1000 ? $price : 1);
            $produit->setBoutique($lBoutique[random_int(0, count($lBoutique) - 1)]);
            $produit->setCodeProduit($this->getUniqueCodeProduit());

            for ($j = 0; $j < 2; $j++) {

                $produitObject = new ProduitObject();
                $produitObject->setSrc($listImageProduit[random_int(0, count($listImageProduit) - 1)]);
                $produitObject->setProduit($produit);
                $this->em->persist($produitObject);
            }
            $this->em->persist($produit);
        }

        $this->em->flush();
        return
            new JsonResponse(
                [
                    'listImageProduit '
                    =>
                    $listImageProduit,
                ],
                200
            );
    }

    /**
     * @Route("/short/faker", name="FakerShort", methods={"GET"})
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
     * 
     * 
     */
    public function FakerShort()
    {


        $listVideoShort = [];
        $sourceDirShort = 'videos/shorts';
        $filesShort  = scandir($sourceDirShort);
        foreach ($filesShort  as $file) {
            if ($file != '.' && $file != '..')
                $listVideoShort[] = $file;
        }



        $paragraphs = "Irure elit Lorem incididunt enim pariatur exercitation deserunt sunt consequat quis nisi veniam do. Adipisicing voluptate occaecat id dolore duis incididunt eu occaecat tempor Lorem adipisicing. Sunt officia elit ex anim consectetur
             proident occaec ";

        $lBoutique = $this->em->getRepository(Boutique::class)->findAll();
        $lProduit = $this->em->getRepository(Produit::class)->findAll();


        for ($i = 0; $i < 10; $i++) {
            # code...

            $faker = Factory::create();


            $codeShort
                = $this->myFunction->getUniqueNameShort();


            $short = new Short();
            $short->setSrc($listVideoShort[random_int(0, count($listVideoShort) - 1)]);
            $short->setTitre($faker->company);
            $short->setCodeShort($codeShort);
            $short->setDescription(
                $paragraphs
            );
            $short->setBoutique($lBoutique[random_int(0, count($lBoutique) - 1)]);
            $this->em->persist($short);


            $produit_short = new ListProduitShort();
            $produit_short->setShort($short);
            $produit_short->setProduit($lProduit[random_int(0, count($lProduit) - 1)]);

            $this->em->persist($produit_short);
        }

        $this->em->flush();
        return
            new JsonResponse(
                [
                    'listVideoShort '
                    =>
                    $listVideoShort,
                ],
                200
            );
    }

    public function getUniqueCodeBoutique()
    {


        $chaine = 'boutique';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        // $ExistCode = $this->em->getRepository(Boutique::class)->findOneBy(['codeBoutique' => $chaine]);
        // if ($ExistCode) {
        //     return
        //         $this->getUniqueCodeBoutique();
        // } else {
        return $chaine;
        // }
    }
    public function getUniqueCodeProduit()
    {


        $chaine = 'produit';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        // $ExistCode = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $chaine]);
        // if ($ExistCode) {
        // return
        //     $this->getUniqueCodeProduit();
        // } else {
        return $chaine;
        // }
    }
}
