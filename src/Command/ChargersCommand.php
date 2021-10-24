<?php

namespace App\Command;

use App\Domain\ZaptecAPI;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:chargers',
    description: 'Add a short description for your command',
)]
class ChargersCommand extends Command
{
    public function __construct(private ZaptecAPI $zaptecAPI)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $chargers = $this->zaptecAPI->getChargers();

        $table = [];
        foreach ($chargers->getData() as $charger) {
            $table[] = [
                $charger->getId(),
                $charger->getName(),
            ];
        }

        $io->table(['id', 'name'], $table);

        return Command::SUCCESS;
    }
}
