<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle;

use IM\Fabric\Package\Datadog\Event;
use IM\Fabric\Package\Datadog\Statsd;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class Datadog
{
    public function __construct(
        private string $appName,
        private Statsd $statsd,
        private LoggerInterface $logger
    ) {
    }

    public function sendEvent(
        string $title,
        string $text,
        string $alertType = Event::ALERT_INFO,
        array $tags = []
    ): void {
        try {
            $event = new Event($title, $text, $this->appName);
            $event->setAlertType($alertType);
            if (!empty($tags)) {
                foreach ($tags as $key => $tag) {
                    $event->addTag($key, $tag);
                }
            }
            $this->statsd->event($event);
        } catch (Throwable $throwable) {
            $this->logError($throwable);
        }
    }

    private function logError(Throwable $throwable): void
    {
        $this->logger->warning('Failed to send event to Datadog', [
            'statusCode' => $throwable->getCode(),
            'error' => $throwable->getMessage()
        ]);
    }
}
