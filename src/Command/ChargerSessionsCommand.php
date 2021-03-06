<?php

namespace App\Command;

use App\Domain\ElspotPrice;
use App\Domain\ZaptecAPI;
use DateInterval;
use DateTime;
use DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:chargers:sessions',
    description: 'Add a short description for your command',
)]
class ChargerSessionsCommand extends Command
{
    public function __construct(private ZaptecAPI $zaptecAPI, private ElspotPrice $elspotPrice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('charger-id', InputArgument::REQUIRED, 'Charger ID')
            ->addOption('include-price', 'p', InputOption::VALUE_NONE, 'Include price')
            ->addOption('region', 'r', InputOption::VALUE_REQUIRED, 'Region', 'Kr.sand')
            ->addOption('start', null, InputOption::VALUE_REQUIRED, 'Start date, includes sessions ending after at this date')
            ->addOption('end', null, InputOption::VALUE_REQUIRED, 'End date, includes charges stopped before this date.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var ConsoleOutput $output */
        $tableSection = $output->section();
        $pbSection = $output->getErrorOutput();

        $chargerId = $input->getArgument('charger-id');
        $region = $input->getOption('region');
        $addPrice = (bool) $input->getOption('include-price');
        $startDate = $input->getOption('start');
        $endDate = $input->getOption('end');

        if ($startDate) {
            $startDate = new DateTime($startDate);
        }
        if ($endDate) {
            $endDate = new DateTime($endDate);
        }

        $sessions = $this->zaptecAPI->getSessions($chargerId);

        $pb = new ProgressBar($pbSection, count($sessions));

        $table = new Table($tableSection);
        $table->setHeaders(['start', 'stop', 'energy', 'price']);

        $sumEnergy = 0.0;
        $sumPrice = 0.0;
        foreach ($sessions as $key => $session) {
            $chargePrice = 0.0;
            $startTime = $session->getStartDateTime();
            $stopTime = $session->getEndDateTime();

            if ($addPrice) {
                foreach ($session->getEnergyDetails() as $detail) {
                    $include = (!$startDate || ($startDate > $detail->getTimestamp())) && (!$endDate || ($endDate > $detail->getTimestamp()));
                    if (!$include) {
                        continue;
                    }

                    $timestampPrice = $this->elspotPrice->findPrice($detail->getTimestamp(), $region);
                    if ($timestampPrice === null) {
                        throw new DomainException('Could not find price for '.$detail->getTimestamp()->format('c'));
                    }
                    $detailPrice = $detail->getEnergy() * ($timestampPrice / 1000);
                    $chargePrice += $detailPrice;
                }

                if ($chargePrice < 0.01 && (!$startDate || ($startDate > $stopTime)) && (!$endDate || ($endDate > $stopTime))) {
                    $averagePrice = $this->getAveragePrice($session->getStartDateTime(), $session->getEndDateTime(), $region);
                    $chargePrice = $session->getEnergy() * ($averagePrice / 1000);
                }
            }

            $table->addRow([
                $startTime->format('d.m.Y H:i'),
                $stopTime->format('d.m.Y H:i'),
                round($session->getEnergy(), 3).' kW',
                $addPrice ? round($chargePrice, 2).'kr' : '',
            ]);
            $sumPrice += $chargePrice;
            $sumEnergy += $session->getEnergy();
            $pb->advance();
        }

        $table->addRow([' ', '', '', '']);
        $table->addRow([
            'Total: ',
            '',
            $sumEnergy.' kw',
            $addPrice ? round($sumPrice, 2).' kr' : '',
        ]);
        $table->setFooterTitle(count($sessions).'sessions');
        $table->render();

        $pb->finish();

        return Command::SUCCESS;
    }

    protected function getAveragePrice(DateTime $startTime, DateTime $stopTime, string $region): float
    {
        $prices = [];
        $currentTime = clone $startTime;
        while ($currentTime <= $stopTime) {
            $currentPrice = $this->elspotPrice->findPrice($currentTime, $region);
            if ($prices === null) {
                throw new DomainException("Could not find price for {$startTime->format('c')} - {$stopTime->format('c')}");
            }

            $prices[] = $currentPrice;
            $currentTime->add(new DateInterval('PT1H'));
        }

        if (count($prices) === 0) {
            throw new DomainException("Could not find price for {$startTime->format('c')} - {$stopTime->format('c')}");
        }

        return array_sum($prices) / count($prices);
    }
}
