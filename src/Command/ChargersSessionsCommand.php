<?php

namespace App\Command;

use App\Domain\ZaptecAPI;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:chargers:sessions',
    description: 'Add a short description for your command',
)]
class ChargersSessionsCommand extends Command
{
    public function __construct(private ZaptecAPI $zaptecAPI)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('charger-id', InputArgument::REQUIRED, 'Charger ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $chargerId = $input->getArgument('charger-id');

        $sessions = $this->zaptecAPI->getSessions($chargerId);

        $table = [];
        foreach ($sessions as $session) {
            $table[] = [
                $session->getStartDateTime()->format('d.m.Y H:i'),
                $session->getEnergy().' kW',
            ];
        }

        $io->table(['id', 'start', 'energy'], $table);

        return Command::SUCCESS;
    }
}
