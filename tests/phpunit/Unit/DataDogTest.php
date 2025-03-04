<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Unit;

use Exception;
use IM\Fabric\Bundle\AndroidServicesBundle\Datadog;
use IM\Fabric\Package\Datadog\Event;
use IM\Fabric\Package\Datadog\Statsd;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DataDogTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Datadog $datadog;
    private Statsd $statsd;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        // Given
        $this->statsd = Mockery::mock(Statsd::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->datadog = new Datadog('android-service-bundle', $this->statsd, $this->logger);
    }

    public function testSendEventSuccessfully(): void
    {
        // When
        $this->statsd->expects('event')->with(Event::class);
        $this->logger->expects('warning')->never();

        // Then
        $this->datadog->sendEvent('Event title', 'Event text', tags: ['example-tag' => 'example-value']);
    }

    public function testSendEventWithFailure(): void
    {
        // When
        $this->statsd->expects('event')->with(Event::class)->andThrows(Exception::class);
        $this->logger->expects('warning');

        // Then
        $this->datadog->sendEvent('Event title', 'Event text', tags: ['example-tag' => 'example-value']);
    }
}
