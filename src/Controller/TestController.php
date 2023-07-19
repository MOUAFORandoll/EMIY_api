<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Commission;
use App\Entity\Localisation;
use App\Entity\ModePaiement;
use App\Entity\PointLivraison;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Short;
use App\Entity\TypeCommande;
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

class TestController extends AbstractController
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
    // genere moi un code php qui renvoi un nombre au hasard


    /**
     * @Route("/imagestest", name="imagestest", methods={"POST"})
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
    public function imagestest(Request $request, SluggerInterface $slugger)
    {


        $possible = false;



        $file = $request->files->get('file');

        $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilenameData = $slugger->slug($originalFilenameData);
        $newFilenameData =
            $this->myFunction->getUniqueNameProduit() . '.' . $file->guessExtension();
        // Move the file to the directory where brochures are stored
        try {
            // $file->move(
            //       $this->serializerParameter('produits_object'),
            //     $newFilenameData
            // );

            // Load the image using GD
            // $image = imagecreatefromstring(file_get_contents($file->getPathname()));
            // Load the image
            $image = imagecreatefromjpeg($file->getPathname());

            // Set the color of the background you want to remove (white in this example)
            $bgColor = imagecolorallocate($image, 255, 255, 255);

            // Set the tolerance for removing similar colors (50 in this example)
            $tolerance = 50;

            // Remove the background
            imagecolortransparent($image, $bgColor);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            // Save the transparent image to disk
            imagepng($image, 'output0.png');
            file_put_contents('output.png', $image);

            // Free up memory
            imagedestroy($image);
            // Save the transparent image to disk
            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success',
                        'file' =>  $file
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
    }
    /**
     * @Route("/modif", name="modif", methods={"GET"})
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
    public function modif(Request $request, SluggerInterface $slugger)
    {


        $list = [];

        $directory = "./images/produits"; // Remplacez ce chemin par le chemin vers votre répertoire
        $files = scandir($directory);

        foreach ($files as $file) {
            if (!in_array($file, array(".", ".."))) { // Ignorer les fichiers "." et ".."
                $list[] =  $file;
            }
        }
        $lProduitO = $this->em->getRepository(ProduitObject::class)->findAll();
        for ($i = 0; $i < count($lProduitO); $i++) {
            $po = $lProduitO[$i];
            $po->setSrc(
                $list[$i]
            );
            $this->em->persist($po);
        }
        $this->em->flush();

        return
            new JsonResponse(
                [
                    'message'
                    =>  'success',
                    'file' =>  $list
                ],
                200
            );
    }





    /**
     * @Route("/autod", name="d", methods={"GET"})
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
    public function d()
    {


        $dirpath = 'chemin/vers/dossier/';
        if (is_dir($dirpath)) {
            // Effacer tous les fichiers et dossiers dans le dossier
            $files = scandir($dirpath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirpath . $file)) {
                        // Effacer le dossier et son contenu
                        $this->deleteDir($dirpath . $file);
                    } else {
                        // Effacer le fichier
                        unlink($dirpath . $file);
                    }
                }
            }
            // Effacer le dossier lui-même
            rmdir($dirpath);
            echo "Le dossier $dirpath et son contenu ont été supprimés.";
        } else {
            echo "Le dossier $dirpath n'existe pas.";
        }

        // Fonction récursive pour effacer un dossier et son contenu

    }
    function deleteDir($dirpath)
    {
        if (is_dir($dirpath)) {
            $files = scandir($dirpath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirpath . $file)) {
                        $this->deleteDir($dirpath . $file);
                    } else {
                        unlink($dirpath . $file);
                    }
                }
            }
            rmdir($dirpath);
        }
    }











    /**
     * @Route("/test", name="GeneratePdf", methods={"GET"})
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
    public function GeneratePdf(Request $request, SluggerInterface $slugger)
    {

        // Récupérer les données nécessaires pour générer le PDF
        $data = ['$this->getDoctrine()->getRepository(Data::class)->findAll()'];

        // Créer une instance de PDF avec la bibliothèque de votre choix (ex : Dompdf)
        $pdf = new Dompdf();
        $contenu = '';
        for ($i = 0; $i < 10; $i++) {
            $contenu = "<tr>
                    <td>" . $i  . "</td>
                    <td>  John Doe </td>
                    <td>FCFA" . 150.00 . "</td>
                </tr>\n";
        }

        // Générer le contenu du PDF avec les données récupérées
        $html = $this->renderView('pdf/test.html.twig', [
            'data'
            => $contenu,
            'nom' => '',
            'date' => '',
        ]);
        $pdf->loadHtml($html);

        // Rendre le PDF
        $pdf->render();

        // Enregistrer le PDF dans un dossier web accessible au public
        $fileName = 'data.pdf';
        $publicPath = $this->getParameter('kernel.project_dir') . '/public/factures';
        // $pdf->output($publicPath . '/' . $fileName);
        file_put_contents($publicPath . '/' . $fileName, $pdf->output());
        // Créer le lien de téléchargement du fichier PDF
        $downloadUrl = $this->generateUrl('download_pdf', [
            'fileName'
            => $fileName,

        ]);

        // Retourner le lien de téléchargement du fichier PDF
        return $this->json([
            'downloadUrl'
            =>   $downloadUrl

        ]);
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
    public function EmiyInit(Request $request)
    {
        $typeU = $this->InitTypeUser();
        $comminssion = $this->InitCommission();
        $typeT = $this->InitTransaction();
        $typeP = $this->InitPaiement();
        $typeC = $this->InitCommande();
        $typeM = $this->InitModePaiement();
        $boutique = $this->InitBoutique();
        $produit = $this->InitProduit();
        $short = $this->InitShort();
        $point_l = $this->InitPointLivraison();

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
            $typeP, 'boutique' =>
            $boutique,
            'produit' =>
            $produit, 'short' =>
            $short, 'point_l' =>
            $point_l,

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


    public function InitCommission()
    {



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


    public function InitTransaction()
    {

        $t = ['Achat', 'Retrait', 'Depot'];

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

    /**
     * @Route("/emiy/admin/l", name="InitPointLivraison", methods={"GET"})
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
    public function InitPointLivraison()
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
    public function InitPaiement()
    {

        $t = ['Livreur', 'Boutique'];

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
    public function InitCommande()
    {

        $t = ['Commande normale', 'Commande negocie '];

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

    public function InitBoutique()
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

    public function InitProduit()
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

    public function InitShort()
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


        for ($i = 0; $i < 20; $i++) {
            # code...

            $faker = Factory::create();




            $short = new Short();
            $short->setSrc($listVideoShort[random_int(0, count($listVideoShort) - 1)]);
            $short->setTitre($faker->company);
            $short->setDescription(
                $paragraphs
            );
            $short->setBoutique($lBoutique[random_int(0, count($lBoutique) - 1)]);
            $this->em->persist($short);
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




    /**
     * @Route("/test/socket/{indexw}", name="TestSocket", methods={"GET"})
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
    public function TestSocket($indexw)
    {


        // $host = 'http://localhost:3000';
        // $first =   $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        // $content = $first->getContent();
        // $index = strpos($content, 0);
        // $res = json_decode(substr($content, $index + 1), true);
        // $sid = $res['sid'];
        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => '40'
        // ]);
        // $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}");

        $data = [
            'canal' => "cnrKvDIERr",
            'data' => [
                "a" => 'aaaaaaaaaaaaa',
                "b" => 'general'
            ]
        ];

        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s', json_encode($data))
        // ]);
        $this->myFunction->Socekt_Emit("negociation", $data);

        return $this->json([
            'status'
            =>   $data

        ]);
    }

    /**
     * @Route("/test/general/{indexw}", name="TEST", methods={"GET"})
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
    public function TEST()
    {



        return $this->json(
            $_SERVER

        );
    }
    /**
     * @Route("/test/general/{indexw}", name="TestSocketGeneral", methods={"GET"})
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
    public function TestSocketGeneral($indexw)
    {


        // $host = 'http://localhost:3000';
        // $first =   $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        // $content = $first->getContent();
        // $index = strpos($content, 0);
        // $res = json_decode(substr($content, $index + 1), true);
        // $sid = $res['sid'];
        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => '40'
        // ]);
        // $this->clientWeb->request('GET', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}");

        $data = [

            "indexw" => $indexw,
            "message" => 'Id fugiat ex nulla veniam ea exercitation velit dolor ad. Minim exercitation magna officia ipsum. Nostrud eu officia ipsum pariatur cillum. Non labore amet non sunt ullamco eiusmod veniam laboris. Ea occaecat in excepteur velit commodo esse. Consectetur laborum in amet voluptate pariatur.'

        ];

        // $this->clientWeb->request('POST', "{$host}/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s', json_encode($data))
        // ]);
        $this->myFunction->Socekt_Emit_general($data);

        return $this->json([
            'status'
            =>   $data

        ]);
    }

    /**
     * @Route("/video-stream", name="video_stream")
     */
    public function videoStreamAction(Request $request)
    {
        $videoPath = $this->getParameter('kernel.project_dir') .  '/public/videos/shorts/produitZt0RX.mp4';

        $segment = $request->query->get('segment');
        // $segmentPath = $videoPath . '/' . $segment;
        $videoSize = filesize($videoPath);
        $videoMimeType = mime_content_type($videoPath);

        // Créez une réponse en streaming
        $response = new StreamedResponse(function () use ($videoPath) {
            // Ouvrez le fichier vidéo en mode lecture binaire
            $file = fopen($videoPath, 'rb');

            // Écrivez les données en streaming
            while (!feof($file)) {
                echo fread($file, 10);
                flush();
            }

            // Fermez le fichier
            fclose($file);
        });

        // Définissez les en-têtes de réponse appropriés pour le streaming vidéo
        $response->headers->set('Content-Type', $videoMimeType);
        $response->headers->set('Content-Length', $videoSize);
        $response->headers->set('Accept-Ranges', 'bytes');

        return $response;
    }
    /**
     * @Route("/video", name="stream_video")
     */
    public function streamVideo(Request $request,)
    {
        $listVideoShort = [];
        $sourceDirShort = 'videos/shorts';
        $filesShort  = scandir($sourceDirShort);
        foreach ($filesShort  as $file) {
            var_dump($file);
            $this->extractImageFromVideoAction($file);
            // if ($file != '.' && $file != '..')
            //     $listVideoShort[] = $file;
        }





        return new Response('Segment vidéo non trouvé', Response::HTTP_NOT_FOUND);
    }

    public function extractImageFromVideoAction($o)
    {
        $videoPath = $this->getParameter('kernel.project_dir') .  '/public/videos/shorts/' .  $o;
        $imagePath = $this->getParameter('kernel.project_dir') .  '/public/videos/shorts/' . str_replace('mp4', 'jpg', $o);

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


    function Convert($o)
    {

        $originalVideoPath = $this->getParameter('kernel.project_dir') .  '/public/videos/shorts/' .  $o;
        $convertedVideoPath = $this->getParameter('kernel.project_dir') .  '/public/videos/shorts/segments/' .  $o;

        $ffmpegCommand = sprintf(
            'ffmpeg -i %s -c:v copy -c:a copy -movflags faststart %s',
            escapeshellarg($originalVideoPath),
            escapeshellarg($convertedVideoPath)
        );

        // Exécution de la commande FFmpeg
        exec($ffmpegCommand, $output, $returnCode);

        if ($returnCode === 0) {
            // Conversion réussie, le fichier $convertedVideoPath contient la vidéo convertie
            echo 'Conversion de la vidéo réussie !';
        } else {
            // Erreur lors de la conversion
            echo 'Erreur lors de la conversion de la vidéo.';
        }
    }
}
