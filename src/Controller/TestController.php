<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\Produit;
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
use App\FunctionU\MyFunction;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Dompdf\Dompdf;


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

        // // Récupérer les données nécessaires pour générer le PDF
        // $data = ['$this->getDoctrine()->getRepository(Data::class)->findAll()'];

        // // Créer une instance de PDF avec la bibliothèque de votre choix (ex : Dompdf)
        // $pdf = new Dompdf();
        // $contenu = '';
        // for ($i=0; $i < ; $i++) {
        //     $contenu = "<tr>
        //             <td>" . $p  ."</td>
        //             <td>" . John Doe ."</td>
        //             <td>FCFA". 150.00 ."</td>
        //         </tr>\n" ;
        // }

        // // Générer le contenu du PDF avec les données récupérées
        // $html = $this->renderView('pdf/index.html.twig', [
        //     'data'
        //     => $contenu,
        //         'nom'=>'',
        //     'date' => '',
        // ]);
        // $pdf->loadHtml($html);

        // // Rendre le PDF
        // $pdf->render();

        // // Enregistrer le PDF dans un dossier web accessible au public
        // $fileName = 'data.pdf';
        // $publicPath =   $this->serializerParameter('kernel.project_dir') . '/public';
        // // $pdf->output($publicPath . '/' . $fileName);
        // file_put_contents($publicPath . '/' . $fileName, $pdf->output());
        // // Créer le lien de téléchargement du fichier PDF
        // $downloadUrl = $this->generateUrl('download_pdf', [
        //     'fileName' => $fileName,

        // ]);

        // // Retourner le lien de téléchargement du fichier PDF
        // return $this->json([
        //     'downloadUrl' => $downloadUrl,
        // ]);
    }
    /**
     * @Route("/download-pdf/{fileName}", name="download_pdf")
     */
    public function downloadPdf(string $fileName)
    {
        // Récupérer le chemin complet du fichier PDF
        $publicPath =   $this->getParameter('kernel.project_dir') . '/public';
        $filePath = $publicPath . '/' . $fileName;

        // Créer une réponse Symfony à partir du fichier PDF
        $response = new Response(file_get_contents($filePath));

        // Définir les entêtes pour la réponse
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');

        return $response;
    }
}
