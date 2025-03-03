<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Model;

use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherModelInterface;

class AndroidPublisherModel implements AndroidPublisherModelInterface
{
    public function __construct(
        public readonly string $packageName,
        private ?string $subscriptionId = null,
        private ?string $purchaseToken = null,
        private ?string $basePlanId = null,
        private ?string $productId = null,
        private ?array $optParams = null
    ) {
    }

    public function setSubscriptionId(string $subscriptionId): static
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function setPurchaseToken(string $purchaseToken): static
    {
        $this->purchaseToken = $purchaseToken;

        return $this;
    }

    public function setBasePlanId(string $basePlanId): static
    {
        $this->basePlanId = $basePlanId;

        return $this;
    }

    public function setOptParams(array $optParams): static
    {
        $this->optParams = $optParams;

        return $this;
    }

    public function setProductId(string $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getBasePlanId(): ?string
    {
        return $this->basePlanId;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function getPurchaseToken(): ?string
    {
        return $this->purchaseToken;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function getOptParams(): ?array
    {
        return $this->optParams;
    }
}
