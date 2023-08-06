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



    /**
     * @Route("/imagestest", name="imagestest", methods={"GET"})
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
    public function imagestest(Request $request,)
    {


        $possible = false;



        $originalFilenameData = $this->getParameter('kernel.project_dir') .  '/public/images/default/freecoin.jpg';

        try {




            $info = getimagesize($originalFilenameData);
            $imageType = $info[2];

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($originalFilenameData);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($originalFilenameData);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($originalFilenameData);
                    break;
                default:
                    throw new \Exception('Unsupported image format.');
            }

            $imageF
                = $this->getParameter('kernel.project_dir') . '/public/images/default/boutique0.png';

            $bgcolor = array("red" => "255", "green" => "255", "blue" => "255");
            $fuzz = 9;

            // $image = shell_exec('convert ' . $originalFilenameData . ' -fuzz 10%' . $fuzz . '% -transparent "rgb(' . $bgcolor['red'] . ',' . $bgcolor['green'] . ',' . $bgcolor['blue'] . ')" ' . $imageF . '');
            $image = shell_exec('convert ' . $originalFilenameData . ' -fuzz 10% -transparent none ' . $imageF . '');


            return
                new JsonResponse(
                    [
                        'message'
                        =>  'success',
                        'file' =>  $this->getParameter('kernel.project_dir') .  '/public/images/default/boutique0.png'
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
}
