<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Exception\AndroidServiceException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

trait HasAndroidServiceException
{
    /** @throws AndroidServiceException */
    private function throwAndroidServiceException(string $message): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation($message, '', [], '', '', null));

        throw new AndroidServiceException((string) $violations);
    }
}
