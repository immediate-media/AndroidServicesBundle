<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Factory;

use Google\Exception;
use Google\Service\AndroidPublisher;
use JsonException;

readonly class AndroidPublisherService
{
    public function __construct(
        private Authenticator $clientFactory
    ) {
    }

    /**
     * @throws Exception | JsonException
     */
    public function build(): AndroidPublisher
    {
        $client = $this->clientFactory->getAuthenticatedClient(AndroidPublisher::ANDROIDPUBLISHER);
        return new AndroidPublisher($client);
    }
}
