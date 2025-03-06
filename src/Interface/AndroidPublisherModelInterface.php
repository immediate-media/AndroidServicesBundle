<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Interface;

interface AndroidPublisherModelInterface
{
    public function getBasePlanId(): ?string;

    public function getPackageName(): string;

    public function getSubscriptionId(): ?string;

    public function getPurchaseToken(): ?string;

    public function getProductId(): ?string;

    public function getOptParams(): ?array;
}
