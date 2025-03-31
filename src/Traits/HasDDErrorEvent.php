<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Datadog;
use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherModelInterface;
use IM\Fabric\Package\Datadog\Event;
use Psr\Log\LoggerInterface;
use Throwable;

/** @SuppressWarnings("LongVariable") */
trait HasDDErrorEvent
{
    private function sendDDErrorEvent(
        Datadog $dataDog,
        LoggerInterface $logger,
        ?AndroidPublisherModelInterface $androidPublisherModel,
        Throwable $exception,
    ): void {
        try {
            $dataDog->sendEvent(
                'Google API: Purchase Subscription Call Failed',
                json_encode([
                    'exception' => $exception->getMessage(),
                    'packageName' => $androidPublisherModel->packageName ?? '',
                    'subscriptionId' => $androidPublisherModel->subscriptionNotification->subscriptionId ?? '',
                    'purchaseToken' => $androidPublisherModel->subscriptionNotification->purchaseToken ?? ''
                ], JSON_THROW_ON_ERROR),
                Event::ALERT_ERROR,
                [
                    'packageName' => $androidPublisherModel->packageName ?? '',
                    'subscriptionId' => $androidPublisherModel->subscriptionNotification->subscriptionId ?? '',
                    'productId' => $androidPublisherModel->getProductId() ?? '',
                    'appName' => 'android-services-bundle',
                ]
            );
        } catch (Throwable $throwable) {
            $logger->warning('Failed to send event to Datadog', [
                'statusCode' => $throwable->getCode(),
                'error' => $throwable->getMessage()
            ]);
            return;
        }
    }
}
