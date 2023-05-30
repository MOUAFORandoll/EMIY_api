<?php

namespace App\Command;

use App\Controller\CommandeController;
use App\Entity\Category;
use App\Entity\Commission;
use App\Entity\ModePaiement;
use App\Entity\TypePaiement;
use App\Entity\TypeTransaction;
use App\Entity\UserPlateform;
use Dompdf\Dompdf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use App\FunctionU\MyFunction;

class CronVerifyCommande extends Command
{
    private $em;
    private $myFuntion;
    private $cc;

    public function __construct(
        EntityManagerInterface $em,
        CommandeController $cc,
        MyFunction $myFuntion
    ) {

        $this->em        = $em;
        $this->myFuntion = $myFuntion;
        $this->cc = $cc;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:verify-com')
            ->setDescription('Migrate data from MySQL to PostgreSQL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {



        $commandes = $this->em->getRepository(Commande::class)->findBy(['statusBuy' => 0]);

        foreach ($commandes as $commande) {


            $statusCommande = $this->myFuntion->verifyBuy($commande->getToken());
            if ($statusCommande == true) {

                $lpp      =
                    $commande->getPanier()->getListProduitPaniers();
                $produits = [];

                $total = 0;

                foreach ($lpp as $prod) {
                    $produit =
                        $prod->getProduit();
                    if ($produit) {

                        $produits[] = [
                            'nom' =>
                            $produit->getTitre(),
                            'quantite' => $prod->getQuantite(),
                            'prix'
                            => $produit->getPrixUnitaire() * $prod->getQuantite(),
                        ];
                        $total += $produit->getPrixUnitaire() * $prod->getQuantite();
                    }
                }

                $dataPrint = [
                    'nom' =>
                    $commande->getPanier()->getNomClient(),
                    'total'
                    => $total,
                    'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                    'produits' => $produits
                ];
                $pdf       =   $this->cc->GeneratePdf($dataPrint);

                $commande->setStatusBuy(true);

                $this->em->persist($commande);
                $this->em->flush();

                $data = [$commande->getCodeCommande(),  [
                    'status'
                    => true,
                    'pdf' => $pdf,
                    'id' => $commande->getId(),
                    'codeClient' => $commande->getCodeClient(),
                    'codeCommande' => $commande->getCodeCommande(),
                    'date' => date_format($commande->getDateCreated(), 'Y-m-d H:i'),
                    'message' => 'Achat Effectue ,Votre Livraison est en cours, veuillez patienter'
                ]];
                $this->myFuntion->Socekt_Emi($data);
            } else {


                // return new JsonResponse([
                //     'status'
                //     => false,
                //     'message' => 'En attente de validation de votre part'
                // ], 200);
            }
        }

        $output->writeln('Data migration completed.');

        return Command::SUCCESS;
    }
}
