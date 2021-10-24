<?php

namespace App\Domain\Zaptec;

class Session
{
    public function __construct(
        private string $id,
        private string $deviceId,
        private \DateTime $startDateTime,
        private \DateTime $endDateTime,
        private float $energy,
        private int $commitMetadata,
        private ?\DateTime $commitEndDateTime,
        private string $chargerId,
        private string $deviceName,
        private string $externallyEnded,
        private ?array $chargerFirmwareVersion,
        private ?string $signedSession,
    ) {
    }

    public static function fromArray(array $session): self
    {
        return new self(
            id: $session['Id'],
            deviceId: $session['DeviceId'],
            startDateTime: new \DateTime($session['StartDateTime']),
            endDateTime: new \DateTime($session['EndDateTime']),
            energy: $session['Energy'],
            commitMetadata: $session['CommitMetadata'],
            commitEndDateTime: isset($session['CommitEndDateTime']) ? new \DateTime($session['CommitEndDateTime']) : null,
            chargerId: $session['ChargerId'],
            deviceName: $session['DeviceName'],
            externallyEnded: $session['ExternallyEnded'],
            chargerFirmwareVersion: $session['ChargerFirmwareVersion'] ?? null,
            signedSession: $session['SignedSession'] ?? null,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): \DateTime
    {
        return $this->endDateTime;
    }

    public function getEnergy(): float
    {
        return round($this->energy, 3, PHP_ROUND_HALF_UP);
    }

    public function getCommitMetadata(): int
    {
        return $this->commitMetadata;
    }

    public function getCommitEndDateTime(): ?\DateTime
    {
        return $this->commitEndDateTime;
    }

    public function getChargerId(): string
    {
        return $this->chargerId;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getExternallyEnded(): string
    {
        return $this->externallyEnded;
    }

    public function getChargerFirmwareVersion(): ?array
    {
        return $this->chargerFirmwareVersion;
    }

    public function getSignedSession(): ?string
    {
        return $this->signedSession;
    }
}
