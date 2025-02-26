<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AndroidServiceEvent extends Event
{
    public const SUCCESS = 'android.service.success';
    public const SUCCESS_MESSAGE = 'Purchase subscription retrieved successfully';

    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
