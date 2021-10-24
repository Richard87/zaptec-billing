<?php

namespace App\Domain\Zaptec;

class Charger
{
    public function __construct(
        private int    $operatingMode,
        private bool   $isOnline,
        private string $id,
        private string $mID,
        private string $deviceId,
        private string $serialNo,
        private string $name,
        private string $createdOnDate,
        private string $circuitId,
        private bool $active,
        private int $currentUserRoles,
        private string $pin,
        private string $templateId,
        private int $deviceType,
        private string $installationName,
        private string $installationId,
        private int $authenticationType,
        private bool $isAuthorizationRequired
    ) {
    }

    public static function fromArray(mixed $chargerArray): self
    {
        return new self(
            operatingMode: $chargerArray['OperatingMode'],
            isOnline: $chargerArray['IsOnline'],
            id: $chargerArray['Id'],
            mID: $chargerArray['MID'],
            deviceId: $chargerArray['DeviceId'],
            serialNo: $chargerArray['SerialNo'],
            name: $chargerArray['Name'],
            createdOnDate: $chargerArray['CreatedOnDate'],
            circuitId: $chargerArray['CircuitId'],
            active: $chargerArray['Active'],
            currentUserRoles: $chargerArray['CurrentUserRoles'],
            pin: $chargerArray['Pin'],
            templateId: $chargerArray['TemplateId'],
            deviceType: $chargerArray['DeviceType'],
            installationName: $chargerArray['InstallationName'],
            installationId: $chargerArray['InstallationId'],
            authenticationType: $chargerArray['AuthenticationType'],
            isAuthorizationRequired: $chargerArray['IsAuthorizationRequired'],
        );
    }

    public function getOperatingMode(): ?int
    {
        return $this->operatingMode;
    }

    public function isIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMID(): ?string
    {
        return $this->mID;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function getSerialNo(): ?string
    {
        return $this->serialNo;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCreatedOnDate(): ?string
    {
        return $this->createdOnDate;
    }

    public function getCircuitId(): ?string
    {
        return $this->circuitId;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getCurrentUserRoles(): ?int
    {
        return $this->currentUserRoles;
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function getDeviceType(): ?int
    {
        return $this->deviceType;
    }

    public function getInstallationName(): ?string
    {
        return $this->installationName;
    }

    public function getInstallationId(): ?string
    {
        return $this->installationId;
    }

    public function getAuthenticationType(): ?int
    {
        return $this->authenticationType;
    }

    public function isIsAuthorizationRequired(): ?bool
    {
        return $this->isAuthorizationRequired;
    }
}
