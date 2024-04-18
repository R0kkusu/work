<?php

declare(strict_types=1);

namespace Platform\Bundle\AcmeFake\SDK;

use Assert\Assert;
use Asset\Cache\CacheServiceAwareInterface;
use Asset\Cache\CacheServiceAwareTrait;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Platform\Bundle\Tiaxa\PortalOptin\SDK\DTO\Response\PortalResponse;
use Platform\Core\Contract\HttpClientAwareInterface;
use Platform\Core\Contract\HttpClientAwareTrait;
use Platform\Core\Logger\ContextLoggerAwareInterface;
use Platform\Core\Logger\ContextLoggerAwareTrait;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

use function sprintf;

/**
 * Class API
 * @package Platform\Bundle\SDK
 */
class API implements
    ContextLoggerAwareInterface,
    HttpClientAwareInterface,
    CacheServiceAwareInterface,
    SerializerAwareInterface
{
    use ContextLoggerAwareTrait;
    use HttpClientAwareTrait;
    use SerializerAwareTrait;
    use CacheServiceAwareTrait;

    private string $apiUrl;

    public function __construct(string $apiUrl)
    {
        Assert::lazy()
            ->that($apiUrl, 'API URL')->url()
            ->verifyNow();

        $this->apiUrl = $apiUrl;

        $this->setSerializer(new Serializer(
            [
                new ObjectNormalizer(
                    propertyTypeExtractor: new ReflectionExtractor(),
                    defaultContext: [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
                )
            ],
            [new JsonEncoder()]
        ));
    }

    public function generateConsentUrl(string $requestId, string $consentPage, string $landing): string
    {
        return sprintf(
            '%s%s?utm_campaign=%s',
            $consentPage,
            $landing,
            $requestId
        );
    }

    /**
     * @throws GuzzleException
     */
    public function unsubscribeProduct(
        string $user,
        string $password,
        string $appId,
        string $partnerID,
        string $transactionId,
        string $userId,
        string $productId,
        int $channelId
    ): PortalResponse {
        Assert::lazy()
            ->that($transactionId, 'Transaction ID')->uuid()
            ->verifyNow();

        $request = new Request(
            'POST',
            sprintf('%s/%s', $this->apiUrl, 'unSubscribeProduct'),
            ['Accept' => 'application/json']
        );

        $response = $this->client->send($request, [
            'auth' => [$user, $password],
            'json' => [
                'appId' => $appId,
                'partnerID' => $partnerID,
                'transID' => $transactionId,
                'version' => '1.0',
                'userID' => ['ID' => $userId, 'userType' => 0],
                'subInfo' => ['productID' => $productId],
                'channelId' => $channelId
            ]
        ]);

        $this->logger->debug(' unsubscribeProduct Response: ' . $response->getBody());
        return $this->serializer->deserialize($response->getBody(), PortalResponse::class, 'json');
    }
}
