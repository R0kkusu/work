<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK;

use Assert\Assert;
use DateTimeInterface;
use GuzzleHttp\Psr7\Uri;
use Platform\Bundle\MBE\MBE4\SDK\DTO\AuthorizationRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\AuthorizationResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\BaseResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\FollowupDirectCaptureRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\NotificationSubscriptionRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\NotificationTransactionRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\StatusResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionStatusRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionStatusResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionTerminateRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\TransactionStatusRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\TransactionStatusResponse;
use Platform\Core\Contract\HttpClientAwareInterface;
use Platform\Core\Contract\HttpClientAwareTrait;
use Platform\Core\Logger\ContextLoggerAwareInterface;
use Platform\Core\Logger\ContextLoggerAwareTrait;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

use function http_build_query;
use function parse_str;
use function sprintf;

/**
 * Class API
 * @package Platform\Bundle\MBE\MBE4\SDK
 * @property Serializer $serializer
 */
class API implements ContextLoggerAwareInterface, HttpClientAwareInterface, SerializerAwareInterface
{
    use ContextLoggerAwareTrait;
    use HttpClientAwareTrait;
    use SerializerAwareTrait;

    private string $baseUrl;
    private string $billingUrl;

    public function __construct(string $baseUrl, string $billingUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->billingUrl = $billingUrl;

        $this->setSerializer(
            new Serializer([
                new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM]),
                new BackedEnumNormalizer(),
                new ObjectNormalizer(
                    null,
                    null,
                    null,
                    new ReflectionExtractor(),
                    null,
                    null,
                    [ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
                )
            ], [new JsonEncoder()])
        );
    }

    public function generateForwardUrl(AuthorizationRequest $request, bool $isOneshot): string
    {
        $assert = Assert::lazy()
            ->that($request->getUsername(), 'request.username')->regex('/^[-a-zA-Z0-9_]{10,30}$/')->notEmpty()
            ->that($request->getPassword(), 'request.password')->notEmpty()
            ->that($request->getClientId(), 'request.clientId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getServiceId(), 'request.serviceId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getContentClass(), 'request.contentClass')->integer()->notEmpty()
            ->that($request->getDescription(), 'request.description')->regex('/^.{1,100}$/')->notEmpty()
            ->that($request->getClientTransactionId(), 'request.clientTransactionid')->regex('/^[-a-zA-Z0-9_]{1,95}$/')->notEmpty()
            ->that($request->getAmount(), 'request.amount')->integer()->greaterOrEqualThan(0)
            ->that($request->getCallbackUrl(), 'request.callbackUrl')->url()
            ->that($request->getTimestamp(), 'request.timestamp')->notEmpty();
        if ($request instanceof SubscriptionRequest) {
            $assert
                ->that($request->getSubscriptionId(), 'request.subscriptionId')->regex('/^[a-zA-Z0-9]{1,32}$/')->notEmpty()
                ->that($request->getSubscriptionDescription(), 'request.subscriptionDescription')->regex('/^[a-zA-Z0-9 \.,!?\-]{1,20}$/')->notEmpty()
                ->that((string) $request->getSubscriptionInterval(), 'request.subscriptionInterval')->regex('/^[0-9]{1,3}$/')->notEmpty();
        } else {
            //onshot
            $assert->that($request->getCurrency(), 'request.currency')->regex('/^[A-Z]{3}$/');
        }

        $assert->verifyNow();
        return sprintf('%s?%s', $this->baseUrl, http_build_query($request->asArray()));
    }

    public function getCarrierRequest(array $response, string $transactionType): AuthorizationResponse|SubscriptionResponse|NotificationTransactionRequest|NotificationSubscriptionRequest
    {
        return $this->serializer->denormalize($response, $transactionType);
    }

    public function getStatus(SubscriptionStatusRequest|TransactionStatusRequest $request): StatusResponse
    {
        $assert =
            Assert::lazy()
                ->that($request->getUsername(), 'request.username')->regex('/^[-a-zA-Z0-9_]{10,30}$/')->notEmpty()
                ->that($request->getPassword(), 'request.password')->notEmpty()
                ->that($request->getServiceId(), 'request.serviceId')->notEmpty()
                ->that($request->getClientId(), 'request.clientId')->regex('/^[0-9]{5}$/')->notEmpty();
        if ($request instanceof TransactionStatusRequest) {
            $assert->that($request->getClientTransactionId(), 'request.clientTransacitionId')->uuid()->notEmpty();
            $responseClass = TransactionStatusResponse::class;
        } else {
            $assert->that($request->getSubscriptionId(), 'request.subscriptionId')->regex('/^[a-zA-Z0-9]{1,32}$/')->notEmpty();
            $responseClass = SubscriptionStatusResponse::class;
        }
        $assert->verifyNow();
        $queryArgs = $request->asArray();
        $uri = (new Uri(sprintf('%s/http/transaction', $this->billingUrl)))
            ->withQuery(
                http_build_query($queryArgs)
            );

        $response = $this->client->request(
            'POST',
            $uri
        );
        parse_str((string) $response->getBody(), $output);
        /** @var StatusResponse $response */
        $response = $this->serializer->denormalize($output, $responseClass);
        return $response;
    }

    public function followupDirectCapture(FollowupDirectCaptureRequest $request): BaseResponse
    {
        Assert::lazy()
            ->that($request->getUsername(), 'request.username')->regex('/^[-a-zA-Z0-9_]{10,30}$/')->notEmpty()
            ->that($request->getPassword(), 'request.password')->notEmpty()
            ->that($request->getClientId(), 'request.clientId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getServiceId(), 'request.serviceId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getContentClass(), 'request.contentClass')->integer()->notEmpty()
            ->that($request->getDescription(), 'request.description')->regex('/^.{1,100}$/')->notEmpty()
            ->that($request->getClientTransactionId(), 'request.clientTransactionid')->regex('/^[-a-zA-Z0-9_]{1,95}$/')->notEmpty()
            ->that($request->getAmount(), 'request.amount')->integer()->greaterOrEqualThan(0)
            ->that($request->getSubscriptionId(), 'request.subscriptionId')->regex('/^[a-zA-Z0-9]{1,32}$/')->notEmpty()
            ->that($request->getSubscriptionDescription(), 'request.subscriptionDescription')->regex('/^[a-zA-Z0-9 \.,!?\-]{1,20}$/')->notEmpty()
            ->that((string) $request->getSubscriptionInterval(), 'request.subscriptionInterval')->regex('/^[0-9]{1,3}$/')->notEmpty()
            ->that($request->getTimestamp(), 'request.timestamp')->notEmpty()
            ->verifyNow();
        $queryArgs = $request->asArray();
        $uri = (new Uri(sprintf('%s/http/transaction', $this->billingUrl)))
            ->withQuery(
                http_build_query($queryArgs)
            );
        $response = $this->client->request(
            'POST',
            $uri
        );
        parse_str((string) $response->getBody(), $output);
        /** @var BaseResponse $response */
        $response = $this->serializer->denormalize($output, BaseResponse::class);
        return $response;
    }

    public function subscriptionTerminate(SubscriptionTerminateRequest $request): BaseResponse
    {
        Assert::lazy()
            ->that($request->getUsername(), 'request.username')->regex('/^[-a-zA-Z0-9_]{10,30}$/')->notEmpty()
            ->that($request->getPassword(), 'request.password')->notEmpty()
            ->that($request->getClientId(), 'request.clientId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getServiceId(), 'request.serviceId')->regex('/^[0-9]{5}$/')->notEmpty()
            ->that($request->getSubscriberId(), 'request.subscriberId')->regex('/^491[5-7]{1}[0-9]{8,9}$|(^I_.{20,500}$)/')->notEmpty()
            ->that($request->getTimestamp(), 'request.timestamp')->notEmpty()
            ->that($request->getReason(), 'request.reason')->regex('/^[- _*+()&!?:.;,&öäüÄÖÜß\sa-zA-Z0-9]{1,100}$/')->notEmpty()
            ->verifyNow();
        $queryArgs = $request->asArray();
        $uri = (new Uri(sprintf('%s/http/transaction', $this->billingUrl)))
            ->withQuery(
                http_build_query($queryArgs)
            );
        $response = $this->client->request(
            'POST',
            $uri
        );
        parse_str((string) $response->getBody(), $output);
        /** @var BaseResponse $response */
        $response = $this->serializer->denormalize($output, BaseResponse::class);
        return $response;
    }
}
