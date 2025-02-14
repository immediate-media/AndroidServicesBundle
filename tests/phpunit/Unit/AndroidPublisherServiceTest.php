<?php

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Unit;

use Google\Exception;
use Google\Service\AndroidPublisher;
use Google\Service\AndroidPublisher\ListSubscriptionsResponse;
use Google\Service\AndroidPublisher\Resource\MonetizationSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptionsv2;
use IM\Fabric\Bundle\AndroidServicesBundle\AndroidServicesApi;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\AndroidPublisherService;
use IM\Fabric\Bundle\AndroidServicesBundle\Model\AndroidPublisherModel;
use IM\Fabric\Bundle\WebhooksCommonBundle\Client\Google\Object\DeveloperNotification;
use IM\Fabric\Bundle\WebhooksCommonBundle\Client\Google\Object\SubscriptionNotification;
use IM\Fabric\Bundle\WebhooksCommonBundle\Datadog;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AndroidPublisherServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AndroidServicesApi $unit;
    private AndroidPublisherService $serviceFactory;
    private Datadog $datadog;
    private AndroidPublisher $service;
    private PurchasesSubscriptions $purchaseSubs;
    private PurchasesSubscriptionsv2 $purchaseSubsV2;
    private MonetizationSubscriptions $monetizationSubs;

    protected function setUp(): void
    {
        $this->serviceFactory = Mockery::mock(AndroidPublisherService::class);
        $this->datadog = Mockery::mock(Datadog::class);

        $this->service = Mockery::mock(AndroidPublisher::class);
        $this->purchaseSubs = Mockery::mock(PurchasesSubscriptions::class);
        $this->purchaseSubsV2 = Mockery::mock(PurchasesSubscriptionsv2::class);
        $this->monetizationSubs = Mockery::mock(MonetizationSubscriptions::class);

        $this->service->purchases_subscriptions = $this->purchaseSubs;
        $this->service->purchases_subscriptionsv2 = $this->purchaseSubsV2;
        $this->service->monetization_subscriptions = $this->monetizationSubs;

        $this->unit = new AndroidServicesApi($this->serviceFactory, $this->datadog);
    }

    public function testItCannotGetPurchaseSubscriptionDataForNoneSubscriptionNotifications(): void
    {
        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');
        $androidPublisherModel
            ->setPurchaseToken('mock.token')
            ->setSubscriptionId('mock.sub.id');

        $this->serviceFactory->shouldNotHaveReceived('build');

        $this->purchaseSubs->shouldNotHaveReceived('get');
        $this->purchaseSubsV2->shouldNotHaveReceived('get');

        $this->assertNull($this->unit->getPurchaseSubscription($androidPublisherModel));
        $this->assertNull($this->unit->getPurchaseSubscriptionV2($androidPublisherModel));
    }

    public function testItCannotGetPurchaseSubscriptionDataForInvalidApiClients(): void
    {
        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');
        $androidPublisherModel
            ->setPurchaseToken('mock.token')
            ->setSubscriptionId('mock.sub.id');

        $this->serviceFactory->expects('build')
            ->times(2)
            ->andReturn($this->service);

        $this->purchaseSubs->expects('get')
            ->once()
            ->with(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getSubscriptionId(),
                $androidPublisherModel->getPurchaseToken()
            )
            ->andThrows(Exception::class);

        $this->purchaseSubsV2->expects('get')
            ->once()
            ->with(
                $androidPublisherModel->getPackageName(),
                $androidPublisherModel->getPurchaseToken()
            )
            ->andThrows(Exception::class);

        $this->datadog->expects('sendEvent')->times(2);

        $this->assertNull($this->unit->getPurchaseSubscription($androidPublisherModel));
        $this->assertNull($this->unit->getPurchaseSubscriptionV2($androidPublisherModel));
    }

    public function testItCanRetrieveAListOfSubscriptionsForAPackage(): void
    {
        $this->serviceFactory->expects('build')
            ->times(1)
            ->andReturn($this->service);

        $response = new ListSubscriptionsResponse();

        $this->monetizationSubs->expects('listMonetizationSubscriptions')->once()->andReturns($response);

        $androidPublisherModel = new AndroidPublisherModel('mock.app.name');

        $actual = $this->unit->getPackageSubscriptions($androidPublisherModel);

        $this->assertInstanceOf(ListSubscriptionsResponse::class, $actual);
    }
}