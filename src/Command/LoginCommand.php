<?php

namespace App\Command;

use App\Domain\ZaptecAPI;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:login',
    description: 'Add a short description for your command',
)]
class LoginCommand extends Command
{
    public function __construct(
        private ZaptecAPI $zaptecAPI,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);

        $token    = $this->zaptecAPI->getToken();
        $io->info($token);

        $io->success('You are logged in.');

        return Command::SUCCESS;
    }
}
