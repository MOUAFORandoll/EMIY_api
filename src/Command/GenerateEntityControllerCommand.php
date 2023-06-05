<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'GenerateEntityControllerCommand',
    description: 'Add a short description for your command',
)]
class GenerateEntityControllerCommand extends Command
{ // ...
    protected function configure()
    {
        $this
            ->setDescription('Generate entity and controller with CRUD methods')
            ->addArgument('entity', InputArgument::REQUIRED, 'Name of the entity class');
    }
    // ...
    // ...
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getArgument('entity');

        // Génération de l'entité
        $this->callCommand('make:entity', [
            'name' => $entityName,
        ]);

        // Génération du contrôleur
        $this->callCommand('make:controller', [
            'name' => $entityName . 'Controller',
            '--no-interaction' => true,
        ]);

        // Génération des méthodes CRUD dans le contrôleur
        $this->generateCRUDMethods($entityName);

        $output->writeln('Entity and controller generated successfully.');

        return Command::SUCCESS;
    }

    private function callCommand($command, $arguments)
    {
        $application = $this->getApplication();
        $command = $application->find($command);
        $input = new ArrayInput($arguments);
        $output = new NullOutput();
        $command->run($input, $output);
    }

    private function generateCRUDMethods($entityName)
    {
        // Génération des méthodes CRUD (à implémenter selon vos besoins)
        // Exemple : create, read, update, delete

        // ...
    }
// ...

}
