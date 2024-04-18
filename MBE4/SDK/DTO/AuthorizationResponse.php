<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use function implode;
use function md5;

/**
 * Class AuthorizationResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class AuthorizationResponse extends BaseResponse
{
    private string $clientTransactionId;
    private string $subscriberId;
    private ?string $operatorId = null;
    private string $hash;

    public function getClientTransactionId(): string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): AuthorizationResponse
    {
        $this->clientTransactionId = $clientTransactionId;
        return $this;
    }

    public function getSubscriberId(): string
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(string $subscriberId): AuthorizationResponse
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getOperatorId(): ?string
    {
        return $this->operatorId;
    }

    public function setOperatorId(?string $operatorId): AuthorizationResponse
    {
        $this->operatorId = $operatorId;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): AuthorizationResponse
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'transactionid' => $this->getTransactionId(),
            'clienttransactionid' => $this->getClientTransactionId(),
            'responsecode' => $this->getResponseCode(),
            'description' => $this->getDescription(),
            'subscriberid' => $this->getSubscriberId(),
            'operatorid' => $this->getOperatorId(),
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT)
        ];
    }

    public function isValid(string $password): bool
    {
        $computed = md5($password . implode('', $this->propertiesAsArray()));
        return $computed === $this->getHash();
    }
}
