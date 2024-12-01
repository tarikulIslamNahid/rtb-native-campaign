<?php

namespace RTB;

class BidManager
{
    private array $campaigns = [];

    public function addCampaign(Campaign $campaign): void
    {
        $this->campaigns[] = $campaign;
    }

    public function processBidRequest(BidRequest $bidRequest): array
    {
        $matchingCampaigns = $this->findMatchingCampaigns($bidRequest);

        if (empty($matchingCampaigns)) {
            error_log("No matching campaigns found");
            return [
                'id' => $bidRequest->getId(),
                'nbr' => 2
            ];
        }

        // Sort by price in descending order and select highest priced campaign
        usort($matchingCampaigns, function($a, $b) {
            return $b->getPrice() <=> $a->getPrice();
        });

        return $matchingCampaigns[0]->createBidResponse($bidRequest);
    }

    private function findMatchingCampaigns(BidRequest $bidRequest): array
    {
        $matchingCampaigns = [];

        foreach ($this->campaigns as $campaign) {
            // Check app ID match
            if ($campaign->getAppId() !== $bidRequest->getAppId()) {
                error_log("App ID mismatch: Campaign " . $campaign->getAppId() . " != Request " . $bidRequest->getAppId());
                continue;
            }

            // Check OS match (case-sensitive)
            if (strpos($campaign->getOs(), $bidRequest->getOs()) === false) {
                error_log("OS mismatch: Campaign " . $campaign->getOs() . " != Request " . $bidRequest->getOs());
                continue;
            }

            // Check device make match if specified
            if ($campaign->getMake() !== 'No Filter' && $campaign->getMake() !== $bidRequest->getDeviceMake()) {
                error_log("Device make mismatch: Campaign " . $campaign->getMake() . " != Request " . $bidRequest->getDeviceMake());
                continue;
            }

            // Check country match
            if ($campaign->getCountry() !== $bidRequest->getCountry()) {
                error_log("Country mismatch: Campaign " . $campaign->getCountry() . " != Request " . $bidRequest->getCountry());
                continue;
            }

            // Check bid floor
            if ($campaign->getPrice() < $bidRequest->getBidFloor()) {
                error_log("Price below bid floor: Campaign " . $campaign->getPrice() . " < Request " . $bidRequest->getBidFloor());
                continue;
            }

            $matchingCampaigns[] = $campaign;
        }

        return $matchingCampaigns;
    }

    public function clearCampaigns(): void
    {
        $this->campaigns = [];
    }
}
