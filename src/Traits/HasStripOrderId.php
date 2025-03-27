<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

trait HasStripOrderId
{
    private function stripOrderId(?string $orderId): ?string
    {
        if ($orderId === null) {
            return null;
        }

        $parts = explode('.', $orderId, 3);

        if (count($parts) === 3) {
            array_pop($parts);

            return implode('.', $parts);
        }

        return $orderId;
    }
}
