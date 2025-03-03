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
use Google\Service\AndroidPublisher\SubscriptionPurchase;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use IM\Fabric\Bundle\AndroidServicesBundle\Traits\HasAndroidServiceException;
use JsonException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @SuppressWarnings("LongVariable")
 */
class AndroidServicesApi
{
    use HasAndroidServiceException;

    private const string FAIL = 'android.service.failure';
    private const string FAIL_MESSAGE = 'Failed to retrieve purchase subscription';

    public function __construct(
        private readonly AndroidPublisherService  $androidPublisherService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws AndroidServiceException|JsonException
     * @deprecated
     */
    public function getPurchaseSubscription(
        AndroidPublisherModelInterface $androidPublisherModel
    ): ?SubscriptionPurchase {
        if (!$androidPublisherModel->getPurchaseToken() || !$androidPublisherModel->getSubscriptionId()) {
            return null;
        }

        try {
            $service = $this->androidPublisherService->build();
            $result = $service->purchases_subscriptions->get(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getSubscriptionId(),
                $androidPublisherModel->getPurchaseToken()
            );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            throw new AndroidServiceException(
                self::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
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
            throw new AndroidServiceException(
                self::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
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
            throw new AndroidServiceException(
                self::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
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
            throw new AndroidServiceException(
                self::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
    }
}
