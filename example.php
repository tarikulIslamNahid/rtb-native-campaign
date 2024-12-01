<?php

require_once __DIR__ . '/vendor/autoload.php';

use RTB\BidManager;
use RTB\BidRequest;
use RTB\Campaign;

// Complete sample bid request from specification
$bidRequestData = [
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
        ],
        "ext" => [
            "materialType" => 2,
            "pullNewestLive" => 1,
            "isFreeAudit" => 0,
            "codeType" => 1,
            "launchAppType" => ["1", "3"],
            "halfScreenTypes" => ["B", "E"],
            "offlineAd" => 0,
            "offlineAdEnable" => 1,
            "secondCategories" => ["App", "Game"]
        ]
    ]],
    "app" => [
        "id" => "ca-app-pub-2476175026271293~2052525764",
        "name" => "",
        "ver" => "V6.6.18.0",
        "bundle" => "net.bat.store",
        "publisher" => [
            "id" => "ca-app-pub-2476175026271293~2052525764"
        ],
        "storeurl" => "http://static.ahagamecenter.com/aha-game.apk",
        "ext" => [
            "mediaType" => 0,
            "sdkVersionCode" => 303000,
            "psVersion" => 9204202
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
        "carrier" => "",
        "language" => "en",
        "make" => "TECNO",
        "model" => "TECNO KF8",
        "os" => "ANDROID",
        "osv" => "11",
        "w" => 720,
        "h" => 1600,
        "ppi" => 2,
        "connectiontype" => 6,
        "devicetype" => 1,
        "ifa" => "1bbbfc6b-7342-47a9-8648-ab8dca628bd2"
    ],
    "user" => [
        "id" => "1bbbfc6b-7342-47a9-8648-ab8dca628bd2",
        "geo" => [
            "lat" => 0,
            "lon" => 0
        ]
    ],
    "at" => 1,
    "tmax" => 650,
    "bcat" => ["IAB25-2", "IAB25-3", "IAB23-1"],
    "badv" => ["https://www.percent99.com/?utm_source=ew", "https://www.percent99.com"],
    "bapp" => ["com.sgiggle.production"]
];

// Complete sample campaign from specification
$campaignData = [
    "campaignname" => "Transsion_Native_Campaign_Test_Nov_30_2024",
    "advertiser" => "TestGP",
    "code" => "1179674AE0080CB1F",
    "appid" => "net.bat.store",
    "tld" => "https://gamestar.shabox.mobi/",
    "portalname" => "com.imo.android.imoim",
    "creative_type" => "201",
    "creative_id" => 168962,
    "day_capping" => 0,
    "dimension" => "600x600",
    "attribute" => "rich-media",
    "url" => "https://gamestar.shabox.mobi/",
    "billing_id" => "900009",
    "price" => 0.1,
    "bidtype" => "CPM",
    "image_url" => "https://d2v3eqx6ppywls.cloudfront.net/adx_web/image/test/ffa5de1b15cd7827f216fb1e9a17b61625e8d9ad.jpeg",
    "htmltag" => "",
    "from_hour" => "0",
    "to_hour" => "23",
    "hs_os" => "ANDROID",
    "operator" => "Banglalink,GrameenPhone,Robi,Teletalk,Airtel,Wi-Fi",
    "device_make" => "TECNO",
    "country" => "BGD",
    "native_title" => "GameStar",
    "native_type" => "Native Display",
    "native_data_value" => "Play Tournament Game",
    "native_data_cta" => "PLAY N WIN",
    "native_data_rating" => "1",
    "native_data_price" => "1"
];

try {
    // Create bid request instance
    $bidRequest = new BidRequest($bidRequestData);

    // Create campaign instance
    $campaign = new Campaign($campaignData);

    // Create bid manager and process bid
    $bidManager = new BidManager();
    $bidManager->addCampaign($campaign);

    // Get bid response
    $response = $bidManager->processBidRequest($bidRequest);

    // Output response
    echo "Bid Response:\n";
    echo json_encode($response, JSON_PRETTY_PRINT);
    echo "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
