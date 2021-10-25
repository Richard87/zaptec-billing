<?php

namespace App\Domain\Zaptec;

use DateTime;

class SessionEnergyDetail
{
    public function __construct(private DateTime $timestamp, private float $energy)
    {
    }

    public static function fromArray(array $fromArray): self
    {
        return new self(new DateTime($fromArray['Timestamp']), $fromArray['Energy']);
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return float kw used
     */
    public function getEnergy(): float
    {
        return $this->energy;
    }
}
