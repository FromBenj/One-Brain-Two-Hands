<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Service\NomenclatureManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AllowDynamicProperties]
#[AsCommand(
    name: 'nom-data',
    description: 'Import to the database the category entities',
)]
class NomenclatureDataCommand extends Command
{
    public function __construct(NomenclatureManager $nomenclatureManager)
    {
        parent::__construct();
        $this->nomenclatureManager = $nomenclatureManager;

    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Importing data...');
        $io = new SymfonyStyle($input, $output);

        $this->nomenclatureManager->migrateToDatabase();
        $io->success('Import completed successfully.');

        return Command::SUCCESS;
    }
}
