<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Commission;
use App\Entity\ModePaiement;
use App\Entity\TypePaiement;
use App\Entity\TypeTransaction;
use App\Entity\UserPlateform;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

class MigrateToPG extends Command
{
    private $mysqlConnection;
    private $postgresConnection;
    private $entityManager;

    public function __construct(Connection $mysqlConnection, Connection $postgresConnection, EntityManagerInterface $entityManager)
    {
        $this->mysqlConnection = $mysqlConnection;
        $this->postgresConnection = $postgresConnection;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:migrate-data')
            ->setDescription('Migrate data from MySQL to PostgreSQL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Connect to MySQL database
        $this->mysqlConnection->connect();

        // Connect to PostgreSQL database
        $this->postgresConnection->connect();

        // Fetch data from MySQL
        $mysqlData = $this->fetchDataFromMySQL();

        // Insert data into PostgreSQL
        // $this->insertDataIntoPostgreSQL($mysqlData);

        $output->writeln('Data migration completed.');

        return Command::SUCCESS;
    }

    private function fetchDataFromMySQL()
    {
        $cat = [

            ['Electronique', 'description*****'],
            [
                'Alimentaire',
                'description*****'
            ],
            [
                'Electro-Menager',
                'description*****'
            ],
            [
                'Vetements',
                'description*****'
            ],
            [
                'Ustenciles',
                'description*****'
            ],
            ['Super-marche', 'Super-marche']
        ];
        foreach ($cat as $c) {
            # code...

            $cat = new Category();
            $cat->setLibelle($c[0]);
            $cat->setDescription($c[1]);
            $this->entityManager->persist($cat);
        }
        $datas = [

            ['Orange Money', 'description*****'],
            [
                'Carte',
                'description*****'
            ],
            [
                'Momo',
                'description*****'
            ],
            [
                'free coin',
                'description*****'
            ],

        ];
        foreach ($datas as $c) {
            $datas = new ModePaiement();
            $datas->setLibelle($c[0]);
            $this->entityManager->persist($datas);
        }
        $datea = [

            'livreur', "Boutique"

        ];
        foreach ($datea as $c) {
            $datas = new TypePaiement();
            $datas->setLibelle($c);
            $this->entityManager->persist($datas);
        }
        $ret = [

            'Achat', "Retrait", "Depot"

        ];
        foreach ($ret as $c) {
            $datas = new TypeTransaction();
            $datas->setLibelle($c);
            $this->entityManager->persist($datas);
        }
        $retuser = [

            'admin', "client", "livreur"

        ];
        foreach ($retuser as $c) {
            $datas = new TypeTransaction();
            $datas->setLibelle($c);
            $this->entityManager->persist($datas);
        }
        $datas = new Commission();
        $datas->setPourcentageProduit(2);
        $datas->setFraisLivraisonProduit(250);
        $datas->setFraisBuyLivreur(500);
        $this->entityManager->persist($datas);


        $users = new UserPlateform();
        $users->setNom('admin');
        $users->setPrenom('mouafo');
        $users->setEmail('h@4.com');
        $users->setPhone(690863838);
        $users->setPassword('$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G');
        $users->setEmail(500);
        $this->entityManager->persist($users);

        $this->entityManager->flush();
        var_dump('ok');
    }
}
