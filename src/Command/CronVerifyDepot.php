<?php

namespace App\Command;

use App\Controller\CommandeController;
use App\Entity\Compte;
use App\Entity\Transaction;
use App\Entity\Commission;
use App\Entity\ModePaiement;
use App\Entity\TypePaiement;
use App\Entity\TypeTransaction;
use App\Entity\UserPlateform;
use Dompdf\Dompdf;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use App\FunctionU\MyFunction;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

class CronVerifyDepot extends  Command
{
    private $em;
    private $myFunction;
    private $cc;

    public function __construct(
        EntityManagerInterface $em,
        CommandeController $cc,
        MyFunction $myFunction
    ) {

        $this->em        = $em;
        $this->myFunction = $myFunction;
        $this->cc        = $cc;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:verify-depot')
            ->setDescription('Cron de verification des depots');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {



        $output->writeln('Debut verify depot.');
        $transactions = $this->em->getRepository(Transaction::class)->findBy(['status' => false]);

        foreach ($transactions as $transaction) {
            var_dump($transaction->getId(),);


            $statusTransaction =
                $this->myFunction->verifyBuy($transaction->getToken());
            $user =
                $transaction->getClient();
            if ($user) {
                if ($statusTransaction && !$transaction->isStatus()) {



                    $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' =>  $user]);
                    $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                    $transaction->setStatus(true);
                    $this->em->persist(
                        $transaction
                    );
                    $this->em->persist(
                        $compte
                    );

                    $this->em->flush();


                    $compte = $this->em->getRepository(Compte::class)->findOneBy(['user' => $user]);
                    $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                    $transaction->setStatus(true);
                    $this->em->persist(
                        $transaction
                    );
                    $this->em->persist(
                        $compte
                    );
                    var_dump($transaction->getToken(),);
                    $this->em->flush();
                    $data = [
                        'canal' =>
                        $transaction->getToken(),
                        'data' => [
                            'status'
                            => true,
                            'message' => 'Recharge Effectue'
                        ]
                    ];
                    $this->myFunction->Socekt_Emit('transaction', $data);
                }
            }
        }

        $output->writeln('Fini. depot');

        return  Command::SUCCESS;
    }
}
