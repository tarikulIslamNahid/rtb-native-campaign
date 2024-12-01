<?php

namespace RTB;

class BidRequest
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validateBidRequest();
    }

    private function validateBidRequest(): void
    {
        // Required fields based on sample bid request
        $requiredFields = ['id', 'imp', 'app', 'device'];
        foreach ($requiredFields as $field) {
            if (!isset($this->data[$field])) {
                throw new \InvalidArgumentException("Missing required bid request field: {$field}");
            }
        }

        // Validate impression array
        if (!isset($this->data['imp'][0])) {
            throw new \InvalidArgumentException("Missing impression data");
        }

        // Validate native ad request
        if (!isset($this->data['imp'][0]['native']['request'])) {
            throw new \InvalidArgumentException("Missing native ad request");
        }
    }

    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getImpressionId(): string
    {
        return $this->data['imp'][0]['id'];
    }

    public function getBidFloor(): float
    {
        return (float)($this->data['imp'][0]['bidfloor'] ?? 0.0);
    }

    public function getAppId(): string
    {
        return $this->data['app']['bundle'] ?? '';
    }

    public function getOs(): string
    {
        return $this->data['device']['os'] ?? '';
    }

    public function getDeviceMake(): string
    {
        return $this->data['device']['make'] ?? '';
    }

    public function getCountry(): string
    {
        return $this->data['device']['geo']['country'] ?? '';
    }

    public function getNativeRequest(): array
    {
        $nativeRequest = $this->data['imp'][0]['native']['request'] ?? '{}';
        return json_decode($nativeRequest, true) ?: [];
    }

    public function getDeviceType(): int
    {
        return (int)($this->data['device']['devicetype'] ?? 0);
    }

    public function getSecure(): int
    {
        return (int)($this->data['imp'][0]['secure'] ?? 0);
    }

    public function getCurrency(): string
    {
        return $this->data['imp'][0]['bidfloorcur'] ?? 'USD';
    }
}
