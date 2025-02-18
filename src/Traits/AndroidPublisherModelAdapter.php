<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Traits;

use IM\Fabric\Bundle\AndroidServicesBundle\Interface\AndroidPublisherDataInterface;
use IM\Fabric\Bundle\AndroidServicesBundle\Model\AndroidPublisherModel;

trait AndroidPublisherModelAdapter
{
    public function androidPublisherModelAdapter(AndroidPublisherDataInterface $notificationData): AndroidPublisherModel
    {
        return (new AndroidPublisherModel($notificationData->getPackageName()));
    }
}
