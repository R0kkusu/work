<?php

declare(strict_types=1);

namespace Platform\Bundle\DMB\Nomad\SDK\Tests;

use Assert\LazyAssertionException;
use Asset\Security\Cipher\AES;
use Asset\Security\Cipher\OpenSSLException;
use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Platform\Bundle\AcmeFake\SDK\API as SUT;
use Platform\Bundle\AcmeFake\SDK\DTO\UnsubscribeRequest;
use Platform\Core\Logger\ContextLoggerInterface;
use Psr\Http\Message\ResponseInterface;

use function parse_str;
use function round;
use function sprintf;

final class APITest extends Unit
{
    private const HE_URL = 'http://consent.training.bundle.one.com';
    private const SUBSCRIPTION_URL = 'http://billing.training.bundle.one.com';
    private const USERNAME = 'digitalvirgo';
    private const PASSWORD = 'secretpassword';
    private const MSISDN = '491511234567';

    private SUT $sut;

    public function setUp(): void
    {
        $this->sut = new SUT(self::HE_URL, self::SUBSCRIPTION_URL);
        $this->sut->setLogger($this->makeEmpty(ContextLoggerInterface::class));
        $this->sut->setClient(new Client());
        $this->sut->setCipher(AES::cipher(AES::AES128, AES::MODE_ECB));
    }


    public function testUnsubscriptionSuccess(): void
    {
        $response = $this->sut->unsubscription(
            new UnsubscribeRequest(self::USERNAME, self::PASSWORD, '200')
        );

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(200, $response->getStatusCode());
    }
}
