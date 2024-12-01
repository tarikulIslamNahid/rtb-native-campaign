# Real-Time Bidding (RTB) Native Campaign System

A PHP-based Real-Time Bidding system for processing native ad campaigns, compliant with OpenRTB 2.5 and Native Ads Specification 1.2.

## Features

- Bid Request Processing
  - Comprehensive validation of incoming bid requests
  - Extraction of device information, geo-location, and ad format parameters
  - Support for native ad request parsing
  - Error handling for malformed requests

- Campaign Management
  - Efficient campaign matching based on multiple criteria:
    * App ID matching
    * OS compatibility
    * Device make filtering
    * Geographical targeting
    * Bid floor validation
  - Highest price campaign selection for multiple matches

- Native Ad Response Generation
  - OpenRTB 2.5 compliant bid responses
  - Support for multiple native ad assets:
    * Title
    * Description
    * Icon
    * Main image
    * Call to action
  - Dynamic impression and click tracking
  - Price conversion to micros format

## Requirements

- PHP 8.2 or higher
- Composer for dependency management
- PHPUnit 11.4.4 for testing

## Installation

1. Clone the repository
2. Install dependencies:
```bash
composer install
```

## Usage

### Basic Usage

```php
use RTB\BidManager;
use RTB\BidRequest;
use RTB\Campaign;

// Create bid request
$bidRequest = new BidRequest($bidRequestData);

// Create campaign
$campaign = new Campaign($campaignData);

// Process bid
$bidManager = new BidManager();
$bidManager->addCampaign($campaign);
$response = $bidManager->processBidRequest($bidRequest);
```

### Bid Request Format

The system accepts bid requests in OpenRTB 2.5 format. Example:

```json
{
    "id": "64dd7619-5723-450b-ab12-36b3367fae97",
    "imp": [{
        "id": "1",
        "bidfloor": 0.1,
        "bidfloorcur": "USD",
        "native": {
            "request": "...",
            "ver": "1.2"
        }
    }],
    "app": {
        "bundle": "net.bat.store"
    },
    "device": {
        "make": "TECNO",
        "os": "ANDROID",
        "geo": {
            "country": "BGD"
        }
    }
}
```

### Campaign Format

Campaigns should be provided with the following required fields:

```php
$campaign = [
    "campaignname" => "Campaign Name",
    "advertiser" => "Advertiser Name",
    "code" => "Campaign Code",
    "appid" => "App Bundle ID",
    "creative_id" => "Creative ID",
    "price" => 0.1,
    "url" => "Landing Page URL",
    "image_url" => "Creative Image URL",
    "hs_os" => "Target OS",
    "device_make" => "Target Device",
    "country" => "Target Country",
    "native_title" => "Ad Title",
    "native_data_value" => "Description",
    "native_data_cta" => "Call to Action"
];
```

### Bid Response Format

The system generates bid responses in OpenRTB 2.5 format:

```json
{
    "id": "response-id",
    "bidid": "bid-id",
    "seatbid": [{
        "bid": [{
            "price": 100000,
            "adm": "{native-ad-markup}",
            "id": "bid-id",
            "impid": "1",
            "crid": "creative-id",
            "bundle": "app-bundle"
        }],
        "seat": "1003",
        "group": 0
    }]
}
```

## Testing

Run the test suite:

```bash
vendor/bin/phpunit tests/test.php
```

 