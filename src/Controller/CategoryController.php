<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\BoutiqueObject;
use App\Entity\Category;
use App\Entity\Produit;
use App\Entity\ProduitObject;
use App\Entity\Transaction;
use App\FunctionU\MyFunction;
use FFI\Exception;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\UserPlateform;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{


    private $em;
    private   $serializer;
    private $clientWeb;
    private $myFunction;

    public function __construct(
        SerializerInterface $serializer,
        MyFunction  $myFunction,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb

    ) {
        $this->em = $em;
        $this->myFunction = $myFunction;
        $this->serializer = $serializer;
        $this->clientWeb = $clientWeb;
    }

    public function getUniqueCodeProduit()
    {


        $chaine = 'produit';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(Produit::class)->findOneBy(['codeProduit' => $chaine]);
        if ($ExistCode) {
            return
                $this->getUniqueCodeProduit();
        } else {
            return $chaine;
        }
    }


    /**
     * @Route("/category/read", name="categoryRead", methods={"GET"})
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
     */
    public function categoryRead(Request $request)
    {

        $possible = false;




        $lCategory = $this->em->getRepository(Category::class)->findAll();

        if ($lCategory) {

            $lC = [];
            foreach ($lCategory  as $category) {

                if ($category->isStatus()) {
                    $categoryU =  [
                        'id' => $category->getId(),
                        'libelle' => $category->getLibelle(),
                        'logo' => 'http' . '://' . $_SERVER['HTTP_HOST'] . '/images/category/' . $category->getLogo(),
                        'description' => $category->getDescription(),
                        // 'titre' => $category->getTitre(), 
                        'status' => $category->isStatus(),

                    ];
                    array_push($lC, $categoryU);
                }
            }
            $lCategoryF =   $this->serializer->serialize($lC, 'json');

            return
                new JsonResponse(
                    [
                        'data'
                        => JSON_DECODE($lCategoryF)
                    ],
                    200
                );
        } else {
            return
                new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Aucun produit'
                ], 203);
        }
    } 
}
