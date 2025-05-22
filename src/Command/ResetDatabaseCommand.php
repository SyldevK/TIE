<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:reset-db',
    description: 'Vide toutes les tables, réinitialise les auto-increments et recharge les fixtures.',
)]
class ResetDatabaseCommand extends Command
{
    private EntityManagerInterface $em;
    private Connection $connection;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
        $this->connection = $em->getConnection();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $platform = $this->connection->getDatabasePlatform()->getName();

        $io->warning('⚠️ Toutes les données vont être supprimées !');

        $tables = ['reservation', 'participant', 'enrollment', 'event_date', 'event', 'user'];
        foreach ($tables as $table) {
            $this->connection->executeStatement("DELETE FROM `$table`");
            $this->connection->executeStatement("ALTER TABLE `$table` AUTO_INCREMENT = 1");
        }

        $io->success('✅ Données supprimées et auto-increments réinitialisés.');

        // Recharge les fixtures
        $process = new Process(['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction']);
        $process->run(function ($type, $buffer) use ($io) {
            $io->writeln($buffer);
        });

        return Command::SUCCESS;
    }
}
