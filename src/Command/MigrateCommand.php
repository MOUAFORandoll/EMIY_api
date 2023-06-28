<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:migrate',
    description: 'Migrations',
)]
class MigrateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();
        // Delete existing migration files
        $migrationFiles = glob('src/migrations/*.php');
        foreach ($migrationFiles as $file) {
            if (is_file($file)) {
                unlink($file);
                var_dump( 'ddd' );
            }
        }

        // Clear migration directory
        $migrationDirectory = 'src/migrations';
        $this->clearDirectory($migrationDirectory);

        $command = "php bin/console make:migration";
        exec($command);
        $io->success(exec($command));

        $command0 = "php bin/console doctrine:migrations:migrate";
        exec($command0);

        $io->success(exec($command0));
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    /**
     * Recursively clears a directory.
     *
     * @param string $directory
     */
    private function clearDirectory(string $directory): void
    {
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file)) {
                $this->clearDirectory($file);
                rmdir($file);
            }
        }
    }
}
