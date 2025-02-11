<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle;

use IM\Fabric\Bundle\AndroidServicesBundle\Factory\AndroidPublisherService;
use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherModelInterface;
use Google\Service\AndroidPublisher\ListSubscriptionOffersResponse;
use Google\Service\AndroidPublisher\ListSubscriptionsResponse;
use Google\Service\AndroidPublisher\SubscriptionPurchase;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use IM\Fabric\Bundle\WebhooksCommonBundle\Datadog;
use IM\Fabric\Package\Datadog\Event;
use Throwable;

readonly class AndroidServicesApi
{
    public function __construct(
        private AndroidPublisherService $serviceFactory,
        private Datadog                 $datadog
    )
    {
    }


    /**
     * @info currently used by sub-notification-api only,
     * should be removed once sub-notification-api is updated
     * @dprecated
     */
    public function getPurchaseSubscription(AndroidPublisherModelInterface $androidPublisherModel): ?SubscriptionPurchase
    {
        if (!$androidPublisherModel->getPurchaseToken() || !$androidPublisherModel->getSubscriptionId()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            return $service->purchases_subscriptions->get(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getSubscriptionId(),
                $androidPublisherModel->getPurchaseToken()
            );
        } catch (Throwable $exception) {
            $this->sendDDErrorEvent($exception, $androidPublisherModel);
            return null;
        }
    }

    public function getPurchaseSubscriptionV2(AndroidPublisherModelInterface $androidPublisherModel): ?SubscriptionPurchaseV2
    {
        if (!$androidPublisherModel->getPurchaseToken()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            return $service
                ->purchases_subscriptionsv2
                ->get(
                    $androidPublisherModel->getPackageName(),
                    $androidPublisherModel->getPurchaseToken()
                );
        } catch (Throwable $exception) {
            $this->sendDDErrorEvent($exception, $androidPublisherModel);
            return null;
        }
    }

    public function getBasePlanOffers(AndroidPublisherModelInterface $androidPublisherModel): ?ListSubscriptionOffersResponse
    {
        if (!$androidPublisherModel->getProductId() || !$androidPublisherModel->getBasePlanId()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            return $service
                ->monetization_subscriptions_basePlans_offers
                ->listMonetizationSubscriptionsBasePlansOffers(
                    $androidPublisherModel->getPackageName(),
                    $androidPublisherModel->getProductId(),
                    $androidPublisherModel->getBasePlanId()
                );
        } catch (Throwable $exception) {
            $this->sendDDErrorEvent($exception, null);
            return null;
        }
    }

    public function getPackageSubscriptions(AndroidPublisherModelInterface $androidPublisherModel): ?ListSubscriptionsResponse
    {
        try {
            $service = $this->serviceFactory->build();
            return $service
                ->monetization_subscriptions
                ->listMonetizationSubscriptions(
                    $androidPublisherModel->getPackageName()
                );
        } catch (Throwable $exception) {
            $this->sendDDErrorEvent($exception, null);
            return null;
        }
    }

    private function sendDDErrorEvent(
        Throwable              $exception,
        ?AndroidPublisherModelInterface $androidPublisherModel
    ): void
    {
        try {
            $this->datadog->sendEvent(
                'Google API: Purchase Subscription Call Failed',
                json_encode([
                    'exception' => $exception->getMessage(),
                    'packageName' => $androidPublisherModel->getPackageName() ?? '',
                    'subscriptionId' => $androidPublisherModel->getSubscriptionId() ?? '',
                    'purchaseToken' => $androidPublisherModel->getPurchaseToken() ?? ''
                ], JSON_THROW_ON_ERROR),
                Event::ALERT_ERROR,
            );
        } catch (Throwable) {
            return;
        }
    }
}
