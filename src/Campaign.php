<?php

namespace RTB;

class Campaign
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validateCampaign();
    }

    private function validateCampaign(): void
    {
        $requiredFields = [
            'campaignname', 'advertiser', 'code', 'appid', 'creative_id', 
            'price', 'url', 'image_url', 'native_title', 'native_data_value', 
            'native_data_cta'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($this->data[$field])) {
                throw new \InvalidArgumentException("Missing required campaign field: $field");
            }
        }
    }

    public function createBidResponse(BidRequest $bidRequest): array
    {
        // Generate bid IDs
        $bidId = bin2hex(random_bytes(16));
        $responseId = bin2hex(random_bytes(16));

        // Convert price to micros (multiply by 1,000,000)
        $priceInMicros = (int)($this->getPrice() * 1000000);

        // Create native ad markup
        $nativeAd = [
            'native' => [
                'assets' => [
                    [
                        'id' => 101,
                        'title' => ['text' => $this->data['native_title']],
                        'required' => 1
                    ],
                    [
                        'id' => 104,
                        'img' => [
                            'url' => $this->data['image_url'],
                            'w' => 100,
                            'h' => 100
                        ],
                        'required' => 1
                    ],
                    [
                        'id' => 105,
                        'img' => [
                            'url' => $this->data['image_url'],
                            'w' => 640,
                            'h' => 640,
                            'type' => 3
                        ],
                        'required' => 1
                    ],
                    [
                        'id' => 102,
                        'data' => [
                            'value' => $this->data['native_data_value'],
                            'type' => 2
                        ],
                        'required' => 1
                    ],
                    [
                        'id' => 103,
                        'data' => [
                            'value' => $this->data['native_data_cta'],
                            'type' => 12
                        ],
                        'required' => 1
                    ]
                ],
                'imptrackers' => [
                    "https://et.mobadvent.com/tad/e2/imp?sdkv=&uid=9a4d873cdcde028d5aa8c72138c5fde4&b=&osv=8.0.0&l=ar&swh=1440*720&sid=62106510713&d=1&c=2&cr=1338&cty=MA&cry=60400&aid=&m=Infinix+X608&tid={$responseId}&cid={$this->data['code']}&rts=1542614130093&fill=1&p={$priceInMicros}"
                ],
                'link' => [
                    'url' => $this->data['url'],
                    'fallback' => $this->data['url'] . '/fallback',
                    'clicktrackers' => ["http://www.qq.com"]
                ],
                'ver' => '1.2'
            ]
        ];

        return [
            'id' => $responseId,
            'bidid' => $bidId,
            'seatbid' => [[
                'bid' => [[
                    'price' => $priceInMicros,
                    'adm' => json_encode($nativeAd, JSON_UNESCAPED_SLASHES),
                    'id' => $bidId,
                    'cid' => $this->data['code'],
                    'impid' => $bidRequest->getImpressionId(),
                    'crid' => (string)$this->data['creative_id'],
                    'bundle' => $bidRequest->getAppId()
                ]],
                'seat' => '1003',
                'group' => 0
            ]]
        ];
    }

    public function getName(): string
    {
        return $this->data['campaignname'] ?? '';
    }

    public function getAppId(): string
    {
        return $this->data['appid'] ?? '';
    }

    public function getOs(): string
    {
        return $this->data['hs_os'] ?? '';
    }

    public function getMake(): string
    {
        return $this->data['device_make'] ?? '';
    }

    public function getCountry(): string
    {
        return $this->data['country'] ?? '';
    }

    public function getPrice(): float
    {
        return (float)($this->data['price'] ?? 0.0);
    }
}
