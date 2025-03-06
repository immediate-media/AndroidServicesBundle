<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\Test\Unit\Factory;

use Google\Client;
use Google\Exception;
use IM\Fabric\Bundle\AndroidServicesBundle\Factory\Authenticator;
use JsonException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const  string MOCK_SCOPE = 'https://www.mock.google.scope/some/service';
    private const  string MOCK_CREDENTIALS = '{
        "type": "service_account",
        "project_id": "mock_project_id",
        "private_key_id": "mock_key_id",
        "private_key": "mock_key",
        "client_email": "mock_email",
        "client_id": "mock_client_id",
        "auth_uri": "mock_url",
        "token_uri":  "mock_url",
        "auth_provider_x509_cert_url": "mock_url",
        "client_x509_cert_url": "mock_url"
    }';

    /**@throws Exception*/
    public function testItThrowsAnExceptionIfTheCertIsNotValidJson(): void
    {
        $client = Mockery::mock(Client::class);
        $this->expectException(JsonException::class);
        $unit = new Authenticator('bad json key', $client);
        $unit->getAuthenticatedClient(self::MOCK_SCOPE);
    }

    /**@throws Exception|JsonException*/
    public function testItReturnsAnAuthenticatedClientFromAMatchedApp(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('setAuthConfig');
        $client->expects('setScopes')->with(self::MOCK_SCOPE);
        $unit = new Authenticator(self::MOCK_CREDENTIALS, $client);
        $this->assertSame($client, $unit->getAuthenticatedClient(self::MOCK_SCOPE));
    }
}
