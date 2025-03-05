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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/** @SuppressWarnings("LongVariable") */
class AndroidServicesApi
{
    use HasAndroidServiceException;
    use hasDDErrorEvent;

    private const string FAIL = 'android.service.failure';
    private const string FAIL_MESSAGE = 'Failed to retrieve purchase subscription';

    public function __construct(
        private readonly AndroidPublisherService $androidPublisherService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Datadog $datadog,
    ) {
    }

    /** @throws AndroidServiceException|JsonException */
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
            $this->sendDDErrorEvent($this->datadog,$androidPublisherModel,$exception);
            $this->throwAndroidServiceException(self::FAIL_MESSAGE);
        }
    }

    /** @throws AndroidServiceException|JsonException */
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
            $this->sendDDErrorEvent($this->datadog,$androidPublisherModel,$exception);
            $this->throwAndroidServiceException(self::FAIL_MESSAGE);
        }
    }

    /** @throws AndroidServiceException|JsonException */
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
            $this->sendDDErrorEvent($this->datadog,$androidPublisherModel,$exception);
            $this->throwAndroidServiceException(self::FAIL_MESSAGE);
        }
    }
}
