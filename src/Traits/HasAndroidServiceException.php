<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Exception\AndroidServiceException;

trait HasAndroidServiceException
{
    /** @throws AndroidServiceException */
    private function throwAndroidServiceException(string $message): void
    {
        throw new AndroidServiceException($message);
    }
}
