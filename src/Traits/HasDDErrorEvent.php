<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Datadog;
use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherModelInterface;
use IM\Fabric\Package\Datadog\Event;
use Throwable;

/** @SuppressWarnings("LongVariable") */
trait HasDDErrorEvent
{
    private function sendDDErrorEvent(
        Throwable $exception,
        Datadog $dataDog,
        ?AndroidPublisherModelInterface $androidPublisherModel
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
            );
        } catch (Throwable) {
            return;
        }
    }
}
