<?php

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Traits\HasStripOrderId;
use PHPUnit\Framework\TestCase;

class HasStripOrderIdTest extends TestCase
{
    use HasStripOrderId;

    private string $orderId = 'GPA.3392-3423-5904-58629..3';
    private string $expected = 'GPA.3392-3423-5904-58629';

    public function testCanStripOrderId(): void
    {
        $this->assertSame($this->expected, $this->stripOrderId($this->orderId));
    }

    public function testReturnsNullForNullOrderId(): void
    {
        $this->assertNull($this->stripOrderId(null));
    }

    public function testReturnsOriginalOrderIdIfNoExtraPart(): void
    {
        $this->assertSame($this->expected, $this->stripOrderId($this->expected));
    }
}
