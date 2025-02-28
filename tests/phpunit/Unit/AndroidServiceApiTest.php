<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Unit;

use Google\Exception;
use Google\Service\AndroidPublisher;
use Google\Service\AndroidPublisher\ListSubscriptionsResponse;
use Google\Service\AndroidPublisher\Resource\MonetizationSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptionsv2;
use IM\Fabric\Bundle\AndroidServicesBundle\AndroidServicesApi;
use IM\Fabric\Bundle\AndroidServicesBundle\Exception\AndroidServiceException;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\AndroidPublisherService;
use IM\Fabric\Bundle\AndroidServicesBundle\Model\AndroidPublisherModel;
use JsonException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @SuppressWarnings("LongVariable")
 */
class AndroidServiceApiTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AndroidServicesApi $unit;
    private AndroidPublisherService $serviceFactory;
    private EventDispatcherInterface $eventDispatcher;
    private AndroidPublisher $service;
    private PurchasesSubscriptions $purchaseSubs;
    private PurchasesSubscriptionsv2 $purchaseSubsV2;
    private MonetizationSubscriptions $monetizationSubs;

    protected function setUp(): void
    {
        $this->serviceFactory = Mockery::mock(AndroidPublisherService::class);
        $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);

        $this->service = Mockery::mock(AndroidPublisher::class);
        $this->purchaseSubs = Mockery::mock(PurchasesSubscriptions::class);
        $this->purchaseSubsV2 = Mockery::mock(PurchasesSubscriptionsv2::class);
        $this->monetizationSubs = Mockery::mock(MonetizationSubscriptions::class);

        $this->service->purchases_subscriptions = $this->purchaseSubs;
        $this->service->purchases_subscriptionsv2 = $this->purchaseSubsV2;
        $this->service->monetization_subscriptions = $this->monetizationSubs;

        $this->unit = new AndroidServicesApi($this->serviceFactory, $this->eventDispatcher);
    }

    /**
     * @throws AndroidServiceException | JsonException
     */
    public function testItCannotGetPurchaseSubscriptionDataForNoneSubscriptionNotifications(): void
    {
        // Given
        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');
        $androidPublisherModel->setPurchaseToken('mock.token')
            ->setSubscriptionId('mock.sub.id');

        $this->serviceFactory->expects('build')->twice()->andReturn($this->service);

        // When
        $this->purchaseSubs->expects('get')->once();
        $this->purchaseSubsV2->expects('get')->once();

        // Then
        $this->eventDispatcher->expects('dispatch')->twice();
        $this->assertNull($this->unit->getPurchaseSubscription($androidPublisherModel));
        $this->assertNull($this->unit->getPurchaseSubscriptionV2($androidPublisherModel));
    }

    /**
     * @throws AndroidServiceException|JsonException
     */
    public function testItCannotGetPurchaseSubscriptionDataForInvalidApiClients(): void
    {
        // Given
        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');
        $androidPublisherModel
            ->setPurchaseToken('mock.token')
            ->setSubscriptionId('mock.sub.id');

        $this->serviceFactory->expects('build')
            ->once()
            ->andReturn($this->service);

        // When
        $this->purchaseSubs->expects('get')
            ->once()
            ->with(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getSubscriptionId(),
                $androidPublisherModel->getPurchaseToken()
            )
            ->andThrows(Exception::class);

        // Then
        $this->expectException(AndroidServiceException::class);
        $this->assertNull($this->unit->getPurchaseSubscription($androidPublisherModel));
    }

    /**@throws JsonException*/
    public function testItCannotGetPurchaseSubscriptionV2DataForInvalidApiClients(): void
    {
        // Given
        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');
        $androidPublisherModel
            ->setPurchaseToken('mock.token')
            ->setSubscriptionId('mock.sub.id');

        $this->serviceFactory->expects('build')
            ->once()
            ->andReturn($this->service);

        // When
        $this->purchaseSubsV2->expects('get')
            ->once()
            ->with(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getPurchaseToken()
            )
            ->andThrows(Exception::class);

        // Then
        $this->expectException(AndroidServiceException::class);

        $this->assertNull($this->unit->getPurchaseSubscriptionV2($androidPublisherModel));
    }

    /**@throws AndroidServiceException|JsonException */
    public function testItCanRetrieveAListOfSubscriptionsForAPackage(): void
    {
        // Given
        $this->serviceFactory->expects('build')
            ->times(1)
            ->andReturn($this->service);

        // When
        $this->monetizationSubs->expects('listMonetizationSubscriptions')
            ->once()
            ->andReturns(new ListSubscriptionsResponse());

        // Then
        $this->eventDispatcher->expects('dispatch')->once();

        $actual = $this->unit->getPackageSubscriptions(new AndroidPublisherModel('mock.app.name'));

        $this->assertInstanceOf(ListSubscriptionsResponse::class, $actual);
    }
}
