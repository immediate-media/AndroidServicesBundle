<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AndroidServiceEvent extends Event
{
    public const SUCCESS = 'android.service.success';
    public const SUCCESS_MESSAGE = 'Purchase subscription retrieved successfully';
    public const FAIL = 'android.service.failure';
    public const FAIL_MESSAGE = 'Failed to retrieve purchase subscription';

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
