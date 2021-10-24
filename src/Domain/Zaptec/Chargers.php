<?php

namespace App\Domain\Zaptec;

class Chargers
{
    /**
     * @param int       $pages
     * @param Charger[] $data
     */
    public function __construct(private int $pages, private array $data)
    {
    }

    public static function fromArray(array $chargersArray): self
    {
        return new self(
            pages: $chargersArray['Pages'],
            data: array_map(fn (array $c) => Charger::fromArray($c), $chargersArray['Data'])
        );
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    /**
     * @return list<Charger>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
