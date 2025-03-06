<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AndroidServiceEvent extends Event
{
    public const string SUCCESS = 'android.service.success';
    public const string SUCCESS_MESSAGE = 'Purchase subscription retrieved successfully';

    public function __construct(private readonly string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
