<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Factory;

use Google\Client;
use Google\Exception;
use JsonException;

/**
 * Responsible for providing a Google API Client with the appropriate authentication credentials.
 * Constructor arguments configured via services.yaml
 */
class Authenticator
{
    public function __construct(
        private string $googleCredentials,
        private Client $client
    ) {
    }

    /** @throws Exception|JsonException */
    public function getAuthenticatedClient(string|array $scopes): Client
    {
        $this->client->setAuthConfig($this->getAuthConfig());
        $this->client->setScopes($scopes);
        return $this->client;
    }

    /** @throws JsonException */
    private function getAuthConfig(): array
    {
        return json_decode($this->googleCredentials, true, 512, JSON_THROW_ON_ERROR);
    }
}
