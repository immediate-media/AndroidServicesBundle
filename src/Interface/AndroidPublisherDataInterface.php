<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Interface;

interface AndroidPublisherDataInterface
{
    public function getPackageName(): string;
}