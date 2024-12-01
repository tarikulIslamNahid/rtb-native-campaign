<?php

namespace RTB\Tests;

use PHPUnit\Framework\TestCase;
use RTB\BidManager;
use RTB\Campaign;
use RTB\BidRequest;

class RTBTest extends TestCase
{
    private array $sampleBidRequest = [
        "id" => "64dd7619-5723-450b-ab12-36b3367fae97",
        "imp" => [[
            "id" => "1",
            "displaymanager" => "hisavana",
            "displaymanagerver" => "3.0.3.0",
            "instl" => 0,
            "tagid" => "240118m7K1iqP0",
            "bidfloor" => 0.1,
            "bidfloorcur" => "USD",
            "secure" => 1,
            "native" => [
                "request" => "{\"native\":{\"ver\":\"1.2\",\"assets\":[{\"id\":101,\"required\":1,\"title\":{\"len\":150}},{\"id\":102,\"required\":0,\"data\":{\"type\":2,\"len\":150}},{\"id\":103,\"required\":0,\"data\":{\"type\":12,\"len\":80}},{\"id\":104,\"required\":0,\"img\":{\"type\":1,\"wmin\":50,\"hmin\":50}},{\"id\":105,\"required\":1,\"img\":{\"type\":3,\"wmin\":200,\"hmin\":200}}],\"eventtrackers\":[{\"event\":1,\"methods\":[1]}]}}",
                "ver" => "1.2"
            ]
        ]],
        "app" => [
            "id" => "ca-app-pub-2476175026271293~2052525764",
            "name" => "",
            "ver" => "V6.6.18.0",
            "bundle" => "net.bat.store",
            "publisher" => [
                "id" => "ca-app-pub-2476175026271293~2052525764"
            ]
        ],
        "device" => [
            "ua" => "Mozilla/5.0 (Linux; Android 11; en-us; TECNO KF8 Build/RP1A.200720.011;) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36",
            "ip" => "36.255.82.232",
            "geo" => [
                "country" => "BGD",
                "region" => "",
                "type" => 2
            ],
            "make" => "TECNO",
            "model" => "TECNO KF8",
            "os" => "ANDROID",
            "osv" => "11"
        ]
    ];

    private array $sampleCampaign = [
        "campaignname" => "Transsion_Native_Campaign_Test_Nov_30_2024",
        "advertiser" => "TestGP",
        "code" => "1179674AE0080CB1F",
        "appid" => "net.bat.store",
        "creative_id" => 168962,
        "price" => 0.1,
        "url" => "https://gamestar.shabox.mobi/",
        "image_url" => "https://d2v3eqx6ppywls.cloudfront.net/adx_web/image/test/ffa5de1b15cd7827f216fb1e9a17b61625e8d9ad.jpeg",
        "hs_os" => "ANDROID",
        "device_make" => "TECNO",
        "country" => "BGD",
        "native_title" => "GameStar",
        "native_data_value" => "Play Tournament Game",
        "native_data_cta" => "PLAY N WIN"
    ];

    public function testBidRequestValidation(): void
    {
        $bidRequest = new BidRequest($this->sampleBidRequest);
        
        $this->assertEquals("64dd7619-5723-450b-ab12-36b3367fae97", $bidRequest->getId());
        $this->assertEquals("1", $bidRequest->getImpressionId());
        $this->assertEquals(0.1, $bidRequest->getBidFloor());
        $this->assertEquals("net.bat.store", $bidRequest->getAppId());
        $this->assertEquals("ANDROID", $bidRequest->getOs());
        $this->assertEquals("TECNO", $bidRequest->getDeviceMake());
        $this->assertEquals("BGD", $bidRequest->getCountry());
    }

    public function testCampaignValidation(): void
    {
        $campaign = new Campaign($this->sampleCampaign);
        
        $this->assertEquals("Transsion_Native_Campaign_Test_Nov_30_2024", $campaign->getName());
        $this->assertEquals("net.bat.store", $campaign->getAppId());
        $this->assertEquals("ANDROID", $campaign->getOs());
        $this->assertEquals("TECNO", $campaign->getMake());
        $this->assertEquals("BGD", $campaign->getCountry());
        $this->assertEquals(0.1, $campaign->getPrice());
    }

    public function testBidResponseGeneration(): void
    {
        $bidRequest = new BidRequest($this->sampleBidRequest);
        $campaign = new Campaign($this->sampleCampaign);
        
        $response = $campaign->createBidResponse($bidRequest);
        
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('bidid', $response);
        $this->assertArrayHasKey('seatbid', $response);
        
        $bid = $response['seatbid'][0]['bid'][0];
        $this->assertEquals(100000, $bid['price']); // 0.1 * 1,000,000
        $this->assertEquals("1179674AE0080CB1F", $bid['cid']);
        $this->assertEquals("1", $bid['impid']);
        $this->assertEquals("168962", $bid['crid']);
        $this->assertEquals("net.bat.store", $bid['bundle']);
    }

    public function testNoBidResponse(): void
    {
        $bidManager = new BidManager();
        $bidRequest = new BidRequest($this->sampleBidRequest);
        
        $response = $bidManager->processBidRequest($bidRequest);
        $this->assertArrayHasKey('nbr', $response);
    }

    public function testCampaignMatching(): void
    {
        $bidManager = new BidManager();
        $bidRequest = new BidRequest($this->sampleBidRequest);
        $campaign = new Campaign($this->sampleCampaign);
        
        $bidManager->addCampaign($campaign);
        $response = $bidManager->processBidRequest($bidRequest);
        
        $this->assertArrayNotHasKey('nbr', $response);
        $this->assertArrayHasKey('seatbid', $response);
    }

    public function testHighestPriceCampaignSelection(): void
    {
        $bidManager = new BidManager();
        $bidRequest = new BidRequest($this->sampleBidRequest);
        
        $campaign1 = new Campaign(array_merge($this->sampleCampaign, ['price' => 0.1]));
        $campaign2 = new Campaign(array_merge($this->sampleCampaign, ['price' => 0.2]));
        
        $bidManager->addCampaign($campaign1);
        $bidManager->addCampaign($campaign2);
        
        $response = $bidManager->processBidRequest($bidRequest);
        $this->assertEquals(200000, $response['seatbid'][0]['bid'][0]['price']); // 0.2 * 1,000,000
    }
}
