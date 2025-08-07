<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle;

use Google\Exception;
use IM\Fabric\Bundle\AndroidServicesBundle\Event\AndroidServiceEvent;
use IM\Fabric\Bundle\AndroidServicesBundle\Exception\AndroidServiceException;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\AndroidPublisherService;
use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherModelInterface;
use Google\Service\AndroidPublisher\ListSubscriptionOffersResponse;
use Google\Service\AndroidPublisher\ListSubscriptionsResponse;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use IM\Fabric\Bundle\AndroidServicesBundle\Traits\HasAndroidServiceException;
use IM\Fabric\Bundle\AndroidServicesBundle\Traits\HasDDErrorEvent;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @SuppressWarnings("LongVariable")
 * @link https://developers.google.com/android-publisher/api-ref/rest Google Play API
 * */
class AndroidServicesApi
{
    use HasAndroidServiceException;
    use hasDDErrorEvent;

    public function __construct(
        private readonly AndroidPublisherService $androidPublisherService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Datadog $datadog,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws AndroidServiceException|JsonException
     * @phpcs:disable Generic.Files.LineLength.TooLong
     * @link https://developers.google.com/android-publisher/api-ref/rest/v3/purchases.subscriptionsv2 Purchases.subscriptionsV2
     * @phpcs:enable
     */
    public function getPurchaseSubscriptionV2(
        AndroidPublisherModelInterface $androidPublisherModel
    ): ?SubscriptionPurchaseV2 {
        if (!$androidPublisherModel->getPurchaseToken()) {
            return null;
        }

        try {
            $service = $this->androidPublisherService->build();
            $result = $service
                ->purchases_subscriptionsv2
                ->get($androidPublisherModel->getPackageName(), $androidPublisherModel->getPurchaseToken());

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->sendDDErrorEvent($this->datadog, $this->logger, $androidPublisherModel, $exception);
            $this->throwAndroidServiceException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws AndroidServiceException|JsonException
     * @phpcs:disable Generic.Files.LineLength.TooLong
     * @link https://developers.google.com/android-publisher/api-ref/rest/v3/monetization.subscriptions.basePlans.offers Monetization.subscriptions.basePlans.offers
     * @phpcs:enable
     */
    public function getBasePlanOffers(
        AndroidPublisherModelInterface $androidPublisherModel
    ): ?ListSubscriptionOffersResponse {
        if (!$androidPublisherModel->getProductId() || !$androidPublisherModel->getBasePlanId()) {
            return null;
        }

        try {
            $service = $this->androidPublisherService->build();
            $result = $service
                ->monetization_subscriptions_basePlans_offers
                ->listMonetizationSubscriptionsBasePlansOffers(
                    $androidPublisherModel->getPackageName(),
                    $androidPublisherModel->getProductId(),
                    $androidPublisherModel->getBasePlanId()
                );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->sendDDErrorEvent($this->datadog, $this->logger, $androidPublisherModel, $exception);
            $this->throwAndroidServiceException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws AndroidServiceException|JsonException
     * @phpcs:disable Generic.Files.LineLength.TooLong
     * @link https://developers.google.com/android-publisher/api-ref/rest/v3/monetization.subscriptions Monetization.subscriptions
     * @phpcs:enable
     */
    public function getPackageSubscriptions(
        AndroidPublisherModelInterface $androidPublisherModel
    ): ?ListSubscriptionsResponse {
        try {
            $service = $this->androidPublisherService->build();
            $result = $service
                ->monetization_subscriptions
                ->listMonetizationSubscriptions(
                    $androidPublisherModel->getPackageName()
                );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->sendDDErrorEvent($this->datadog, $this->logger, $androidPublisherModel, $exception);
            $this->throwAndroidServiceException($exception->getMessage(), $exception->getCode());
        }
    }
}
