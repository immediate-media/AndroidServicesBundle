# AndroidServicesBundle

## About

The `AndroidServicesBundle` is a Symfony bundle that facilitates interaction with the Google Android Publisher services.
It provides a set of tools and services and other related functionalities to easily interact with the Google Play Developer API.

You can find all the information regarding what is available in the [Google Play Developer API](https://developers.google.com/android-publisher/api-ref/rest)

### Services
The bundle currently only provide 3x services to interact with the Android Publisher API.
- [PurchaseSubscriptionV2](https://developers.google.com/android-publisher/api-ref/rest/v3/purchases.subscriptionsv2)
- [BasePlanOffers](https://developers.google.com/android-publisher/api-ref/rest/v3/monetization.subscriptions.basePlans.offers)
- [PackageSubscriptions](https://developers.google.com/android-publisher/api-ref/rest/v3/monetization.subscriptions)
---
## Framework and Libraries
- [Symfony 6.4](https://symfony.com/doc/6.4/index.html)
- [immediate/im-datadog](https://github.com/immediatemediaco/im-datadog)
---
## Installation

To install the bundle, first add the package to your `composer.json` file:

```json
  "repositories": {
    "immediate/android-services-bundle": {
      "type": "vcs",
      "url": "https://github.com/immediate-media/AndroidServicesBundle.git",
    }
  }
```
Then run composer require:
```sh
composer require immediate/android-services-bundle:1.0.0
``` 
---
## Configuration

The below should automatically be added to your `config/bundles.php` file by the Symfony recipes after a the composer updated.
If not you will need to add the following to your `config/bundles.php` file:
```php
return [
    // Other bundles...
    IM\Fabric\Bundle\AndroidServicesBundle\AndroidServicesBundle::class => ['all' => true],
];
```

For local testing you will need to make sure that you `.env.local` & `.env.test` files have the following environment variables set:
```dotenv
###> Google API Variables <###
GOOGLE_API_SERVICE_ACCOUNT_CREDENTIALS=
```

The value of `GOOGLE_API_SERVICE_ACCOUNT_CREDENTIALS` should be the JSON string of the service account credentials that you can download from the Google Cloud Console.
or ask one of the developers owner from `@im-polaris` to provide you with the credentials.
---
# Usage

Example: Retrieving a Purchase Subscription

```php
<?php

use IM\Fabric\Bundle\AndroidServicesBundle\AndroidServicesApi;
use IM\Fabric\Bundle\AndroidServicesBundle\Model\AndroidPublisherModel;
use IM\Fabric\Bundle\AndroidServicesBundle\Exception\AndroidServiceException;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use JsonException;

class ExampleService
{
    private AndroidServicesApi $androidServicesApi;

    public function __construct(AndroidServicesApi $androidServicesApi)
    {
        $this->androidServicesApi = $androidServicesApi;
    }

    public function getPurchaseSubscription(string $packageName, string $purchaseToken): ?SubscriptionPurchaseV2
    {
        $androidPublisherModel = new AndroidPublisherModel($packageName);
        $androidPublisherModel->setPurchaseToken($purchaseToken);

        try {
            return $this->androidServicesApi->getPurchaseSubscriptionV2($androidPublisherModel);
        } catch (AndroidServiceException | JsonException $e) {
            // Handle exception
            return null;
        }
    }
}
```
---
## Event and Error Handling

The bundle dispatches an events on Successful call and thrown an `AndroidServiceException` in case of failure,
which you can listen to and handle accordingly.

`AndroidServiceException` extends the `Exception` class and pass the error message and error code response from the Google API.

### Note:
The bundle make use of the `immediate/im-datadog` library to log the error messages and error codes to Datadog for each request failure,
removing the need for your application to handle the logging of the error messages to it.
---
## Testing & Scripts 
The bundle comes with some basic script that you can find within the [composer.json](composer.json) file.:

If you only want a run the tests you can run the following command:
```sh
composer run-tests
```