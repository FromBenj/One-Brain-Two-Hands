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

#[AsCommand(
    name: 'cat:m',
    description: 'Import to the database the category entities',
)]
class CategoryDataCommand extends Command
{
    public function __construct(
        private readonly NomenclatureManager $nomenclatureManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $io = new SymfonyStyle($input, $output);
        $io->title('Importing category data...');
        $io->progressStart();
        $this->nomenclatureManager->migrateToDatabase();
        $io->success('Import completed successfully.');

        return Command::SUCCESS;
    }
}
