<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\Tests;

use Asset\DataManipulation\CarbonDateTime;
use Codeception\Test\Unit;
use GuzzleHttp\Client;
use Platform\Bundle\MBE\MBE4\SDK\API;
use Platform\Bundle\MBE\MBE4\SDK\DTO\AuthorizationRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\BaseResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\FollowupDirectCaptureRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionStatusRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionStatusResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionTerminateRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\TransactionStatusRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\TransactionStatusResponse;

use function md5;
use function parse_str;
use function parse_url;
use function uuid_create;

/**
 * Class APITest
 * @package Platform\Bundle\MBE\MBE4\SDK\Tests
 */
final class APITest extends Unit
{
    public const VALID_USER_NAME = 'UserName_111';
    public const INVALID_USER_NAME = 'UserName_999';
    public const CLIENT_ID = '23123';
    public const SERVICE_ID = '96534';
    public const PASSWORD = 'password';
    public const TRANSACTION_ID = '921';
    public const VALID_DESCRIPTION = 'some_Description';
    public const INVALID_DESCRIPTION = 'some_invalid_Description';
    public const VALID_TRANSACTION_ID = '4321';
    public const INVALID_TRANSACTION_ID = '1234';
    public const VALID_DATE = '2022-10-31T11:20:06.523Z';
    public const INVALID_DATE = '2022-10-31T12:12:16.123Z';

    private string $mockoonUrl = 'http://dvp4m-mock.dve-dev.com:3060/mbe/mbe4';
    private API $sdk;

    public function setUp(): void
    {
        $httpClient = new Client([
            'http_errors' => false,
        ]);
        $this->sdk = new API($this->mockoonUrl, $this->mockoonUrl);
        $this->sdk->setClient($httpClient);
    }

    public function testGenerateForwardUrlOneShotSuccess(): void
    {
        $clientTransactionId = uuid_create();
        $request = (new AuthorizationRequest())
            ->setServiceId(self::SERVICE_ID)
            ->setContentClass(1)
            ->setDescription(self::VALID_DESCRIPTION)
            ->setClientTransactionId($clientTransactionId)
            ->setAmount(100)
            ->setCurrency('PLN')
            ->setCallbackUrl('https://google.com?tid=xx')
            ->setNotificationUrl('https://google.com?tid=yy')
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'))
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID);
        /** @var AuthorizationRequest $request */
        $redirectUrl = $this->sdk->generateForwardUrl($request, true);
        parse_str(parse_url($redirectUrl)['query'], $parsed);
        self::assertSame(self::VALID_USER_NAME, $parsed['username']);
        self::assertSame(self::CLIENT_ID, $parsed['clientid']);
        self::assertSame(self::SERVICE_ID, $parsed['serviceid']);
        self::assertSame('1', $parsed['contentclass']);
        self::assertSame(self::VALID_DESCRIPTION, $parsed['description']);
        self::assertSame($clientTransactionId, $parsed['clienttransactionid']);
        self::assertSame('100', $parsed['amount']);
        self::assertSame('PLN', $parsed['currency']);
        self::assertSame('https://google.com?tid=xx', $parsed['callbackurl']);
        self::assertSame('https://google.com?tid=yy', $parsed['notificationurl']);
        self::assertSame('2009-01-01T10:00:00.000Z', $parsed['timestamp']);
    }

    public function testGenerateForwardUrlSubscriptionSuccess(): void
    {
        $clientTransactionId = uuid_create();
        $request = (new SubscriptionRequest())
            ->setSubscriptionId('123qwe')
            ->setSubscriptionDescription('subs desc')
            ->setSubscriptionInterval(2)
            ->setServiceId(self::SERVICE_ID)
            ->setContentClass(1)
            ->setDescription(self::VALID_DESCRIPTION)
            ->setClientTransactionId($clientTransactionId)
            ->setAmount(100)
            ->setCallbackUrl('https://google.com?tid=xx')
            ->setNotificationUrl('https://google.com?tid=yy')
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'))
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID);
        /** @var SubscriptionRequest $request */
        $redirectUrl = $this->sdk->generateForwardUrl($request, false);
        parse_str(parse_url($redirectUrl)['query'], $parsed);
        self::assertSame(self::VALID_USER_NAME, $parsed['username']);
        self::assertSame(self::CLIENT_ID, $parsed['clientid']);
        self::assertSame(self::SERVICE_ID, $parsed['serviceid']);
        self::assertSame('1', $parsed['contentclass']);
        self::assertSame(self::VALID_DESCRIPTION, $parsed['description']);
        self::assertSame($clientTransactionId, $parsed['clienttransactionid']);
        self::assertSame('100', $parsed['amount']);
        self::assertSame('123qwe', $parsed['subscriptionid']);
        self::assertSame('subs desc', $parsed['subscriptiondescription']);
        self::assertSame('2', $parsed['subscriptioninterval']);
        self::assertSame('https://google.com?tid=xx', $parsed['callbackurl']);
        self::assertSame('https://google.com?tid=yy', $parsed['notificationurl']);
        self::assertSame('2009-01-01T10:00:00.000Z', $parsed['timestamp']);
    }

    public function testGetTransactionStatus(): void
    {
        $clientTransactionId = uuid_create();
        $request = (new TransactionStatusRequest())
            ->setClientTransactionId($clientTransactionId)
            ->setServiceId(self::SERVICE_ID)
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID)
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'));
        /** @var TransactionStatusRequest $request */
        $response = $this->sdk->getStatus($request);
        self::assertInstanceOf(TransactionStatusResponse::class, $response);
    }

    public function testGetSubscriptionStatus(): void
    {
        $subscriptionId = md5(uuid_create());
        $request = (new SubscriptionStatusRequest())
            ->setSubscriptionId($subscriptionId)
            ->setServiceId(self::SERVICE_ID)
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID)
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'));
        /** @var SubscriptionStatusRequest $request */
        $response = $this->sdk->getStatus($request);
        self::assertInstanceOf(SubscriptionStatusResponse::class, $response);
    }

    public function testFollowupDirectCapture(): void
    {
        $clientTransactionId = uuid_create();
        $followupDirectRequest = (new FollowupDirectCaptureRequest())
            ->setSubscriptionDescription('subs desc')
            ->setSubscriptionId('123qwe')
            ->setSubscriptionInterval(2)
            ->setServiceId(self::SERVICE_ID)
            ->setContentClass(1)
            ->setDescription(self::VALID_DESCRIPTION)
            ->setClientTransactionId($clientTransactionId)
            ->setAmount(100)
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'))
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID);
        /** @var FollowupDirectCaptureRequest $followupDirectRequest */
        $response = $this->sdk->followupDirectCapture($followupDirectRequest);
        self::assertInstanceOf(BaseResponse::class, $response);
    }

    public function testSubscriptionTerminate(): void
    {
        $clientTransactionId = uuid_create();
        $terminateRequest = (new SubscriptionTerminateRequest())
            ->setReason('service')
            ->setSubscriberId('4915123456788')
            ->setServiceId(self::SERVICE_ID)
            ->setClientTransactionId($clientTransactionId)
            ->setTimestamp(new CarbonDateTime('2009-01-01T10:00:00.000Z'))
            ->setUsername(self::VALID_USER_NAME)
            ->setPassword(self::PASSWORD)
            ->setClientId(self::CLIENT_ID);
        /** @var SubscriptionTerminateRequest $terminateRequest */
        $response = $this->sdk->subscriptionTerminate($terminateRequest);
        self::assertInstanceOf(BaseResponse::class, $response);
    }
}
