<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Service\AssociationManager;
use App\Service\NomenclatureManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'asso:m',
    description: 'Import to the database the association entities',
)]
class AssociationDataCommand extends Command
{

    public function __construct(
        private readonly AssociationManager $associationManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'department',
            InputArgument::REQUIRED,
            'Department number'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $io = new SymfonyStyle($input, $output);
        $department = (int) $input->getArgument('department');
        $io->title("Importing associations for department $department ...");
        $io->progressStart();
        $this->associationManager->migrateToDatabase($department);
        $io->success('Import completed successfully.');

        return Command::SUCCESS;
    }
}
