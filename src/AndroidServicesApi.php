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
use IM\Fabric\Bundle\AndroidServicesBundle\Traits\ThrowAndroidServiceException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

class AndroidServicesApi
{
    use ThrowAndroidServiceException;

    public function __construct(
        private AndroidPublisherService  $serviceFactory,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }


    /**
     * @info currently used by sub-notification-api only,
     * should be removed once sub-notification-api is updated
     * @dprecated
     * @throws AndroidServiceException
     */
    public function getPurchaseSubscription(AndroidPublisherModelInterface $androidPublisherModel): ?SubscriptionPurchase
    {
        if (!$androidPublisherModel->getPurchaseToken() || !$androidPublisherModel->getSubscriptionId()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            $result = $service->purchases_subscriptions->get(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getSubscriptionId(),
                $androidPublisherModel->getPurchaseToken()
            );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::FAIL_MESSAGE),
                AndroidServiceEvent::FAIL
            );
            throw new AndroidServiceException(
                AndroidServiceEvent::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws AndroidServiceException
     */
    public function getPurchaseSubscriptionV2(AndroidPublisherModelInterface $androidPublisherModel): ?SubscriptionPurchaseV2
    {
        if (!$androidPublisherModel->getPurchaseToken()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            $result = $service
                ->purchases_subscriptionsv2
                ->get($androidPublisherModel->getPackageName(), $androidPublisherModel->getPurchaseToken());

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::FAIL_MESSAGE),
                AndroidServiceEvent::FAIL
            );
            throw new AndroidServiceException(
                AndroidServiceEvent::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws AndroidServiceException
     */
    public function getBasePlanOffers(AndroidPublisherModelInterface $androidPublisherModel): ?ListSubscriptionOffersResponse
    {
        if (!$androidPublisherModel->getProductId() || !$androidPublisherModel->getBasePlanId()) {
            return null;
        }

        try {
            $service = $this->serviceFactory->build();
            $result = $service
                ->monetization_subscriptions_basePlans_offers
                ->listMonetizationSubscriptionsBasePlansOffers(
                    $androidPublisherModel->getPackageName(),
                    $androidPublisherModel->getProductId(),
                    $androidPublisherModel->getBasePlanId()
                );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::FAIL_MESSAGE),
                AndroidServiceEvent::FAIL
            );
            throw new AndroidServiceException(
                AndroidServiceEvent::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws AndroidServiceException
     */
    public function getPackageSubscriptions(AndroidPublisherModelInterface $androidPublisherModel): ?ListSubscriptionsResponse
    {
        try {
            $service = $this->serviceFactory->build();
            $result = $service
                ->monetization_subscriptions
                ->listMonetizationSubscriptions(
                    $androidPublisherModel->getPackageName()
                );

            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::SUCCESS_MESSAGE),
                AndroidServiceEvent::SUCCESS
            );

            return $result;
        } catch (Throwable $exception) {
            $this->eventDispatcher->dispatch(
                new AndroidServiceEvent(
                    AndroidServiceEvent::FAIL_MESSAGE),
                AndroidServiceEvent::FAIL
            );
            throw new AndroidServiceException(
                AndroidServiceEvent::FAIL_MESSAGE,
                $exception->getCode(),
                $exception
            );
        }
    }
}
