<?php

namespace App\Domain;

class ElspotPrice
{
    public const VALID_REGIONS = ['SYS', 'SE1', 'SE2', 'SE3', 'SE4', 'FI', 'DK1', 'DK2',
                                  'Oslo', 'Kr.sand', 'Bergen', 'Molde', 'Tr.heim', 'Tromsø',
                                  'EE', 'LV', 'LT', 'AT', 'BE', 'DE-LU', 'FR', 'NL', ];

    private const SPOT_FILES = [
        2018 => __DIR__.'/../../elspot/elspot-prices_2018_hourly_nok.csv',
        2019 => __DIR__.'/../../elspot/elspot-prices_2019_hourly_nok.csv',
        2020 => __DIR__.'/../../elspot/elspot-prices_2020_hourly_nok.csv',
        2021 => __DIR__.'/../../elspot/elspot-prices_2021_hourly_nok.csv',
    ];

    private array $loadedFiles = [];

    public function findPrice(\DateTime $date, string $region): ?float
    {
        if (!in_array($region, self::VALID_REGIONS)) {
            throw new \DomainException("Invalid region '$region'!");
        }

        $year = (int) $date->format('Y');
        if (!array_key_exists($year, self::SPOT_FILES)) {
            throw new \DomainException("Invalid year '$year'!");
        }

        $sheet = $this->getSheet($year);

        $targetColumn = null;
        foreach ($sheet->getRowIterator(3, 3) as $rowIterator) {
            foreach ($rowIterator->getCellIterator() as $cell) {
                if ($cell->getValue() === $region) {
                    $targetColumn = $cell->getColumn();
                    break 2;
                }
            }
        }

        if ($targetColumn === null) {
            throw new \DomainException('Could not find region column!');
        }

        $targetDate = $date->format('d.m.Y');
        $targetHour = $date->format('H');

        foreach ($sheet->getRowIterator(4) as $row) {
            $currentRow = $row->getRowIndex();
            $date = $sheet->getCellByColumnAndRow(1, $currentRow)->getValue();
            $hour = $sheet->getCellByColumnAndRow(2, $currentRow)->getValue();
            $hour = substr($hour, 0, 2);

            if ($date !== $targetDate || $targetHour !== $hour) {
                continue;
            }

            $targetCell = $sheet->getCell($targetColumn.$currentRow);
            $value = $targetCell->getValue();
            $value = str_replace(',', '.', $value);
            $value = (float) $value;

            return $value;
        }

        return null;
    }

    protected function getSheet(int $year): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        if (!isset($this->loadedFiles[$year])) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(self::SPOT_FILES[$year]);

            $this->loadedFiles[$year] = $spreadsheet->getActiveSheet();
        }

        return $this->loadedFiles[$year];
    }
}
