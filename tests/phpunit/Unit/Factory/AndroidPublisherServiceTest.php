<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Unit\Factory;

use Google\Client;
use Google\Exception;
use Google\Service\AndroidPublisher;
use Google\Service\AndroidPublisher\Resource\MonetizationSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptions;
use Google\Service\AndroidPublisher\Resource\PurchasesSubscriptionsv2;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\AndroidPublisherService;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\Authenticator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AndroidPublisherServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Authenticator $authenticator;

    private AndroidPublisherService $unit;

    public function setup(): void
    {
        $this->authenticator = Mockery::mock(Authenticator::class);
        $this->unit = new AndroidPublisherService($this->authenticator);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testItReturnsAnAuthenticatedAndroidPublisherClient(): void
    {
        $this->authenticator->expects('getAuthenticatedClient')->once()->andReturn(new Client());
        $client = $this->unit->build();
        $this->assertInstanceOf(AndroidPublisher::class, $client);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testTheAndroidPublisherClientContainsPurchaseSubscriptionsComponent(): void
    {
        $this->authenticator->expects('getAuthenticatedClient')->once()->andReturn(new Client());
        $client = $this->unit->build();
        $this->assertInstanceOf(PurchasesSubscriptions::class, $client->purchases_subscriptions);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testTheAndroidPublisherClientContainsPurchaseSubscriptionsV2Component(): void
    {
        $this->authenticator->expects('getAuthenticatedClient')->once()->andReturn(new Client());
        $client = $this->unit->build();
        $this->assertInstanceOf(PurchasesSubscriptionsv2::class, $client->purchases_subscriptionsv2);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testTheAndroidPublisherClientContainsMonetizationSubscriptionsComponent(): void
    {
        $this->authenticator->expects('getAuthenticatedClient')->once()->andReturn(new Client());
        $client = $this->unit->build();
        $this->assertInstanceOf(MonetizationSubscriptions::class, $client->monetization_subscriptions);
    }
}
