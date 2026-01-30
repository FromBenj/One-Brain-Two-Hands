<?php

namespace App\Command;

use App\Service\MapManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'geo:m',
    description: 'Create geocode data from associations addresses'
)]
class GeocodeDataCommand extends Command
{
    public function __construct(
        private readonly MapManager    $mapManager,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Max geocode creations', 200)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)$input->getOption('limit');
        $io->title("Geocoding up to $limit associations");

        $this->mapManager->migrateCoordToDatabase($limit);
        $io->success('Geocoding batch completed.');

        return Command::SUCCESS;
    }
}
