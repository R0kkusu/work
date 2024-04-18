<?php

declare(strict_types=1);

namespace Platform\Bundle\SDK\DTO;

class CallbackDTO
{
    private string $callbackUrl;
    private string $transactionId;
    private string $clientTransactionId;
    private string $responseCode;
    private string $description;
    private string $subscriberId;
    private string $operatorId;
    private string $timestamp;
    private string $subscriptionId;
    private string $hash;

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): CallbackDTO
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): CallbackDTO
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getClientTransactionId(): string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): CallbackDTO
    {
        $this->clientTransactionId = $clientTransactionId;
        return $this;
    }

    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    public function setResponseCode(string $responseCode): CallbackDTO
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): CallbackDTO
    {
        $this->description = $description;
        return $this;
    }

    public function getSubscriberId(): string
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(string $subscriberId): CallbackDTO
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getOperatorId(): string
    {
        return $this->operatorId;
    }

    public function setOperatorId(string $operatorId): CallbackDTO
    {
        $this->operatorId = $operatorId;
        return $this;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): CallbackDTO
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): CallbackDTO
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): CallbackDTO
    {
        $this->hash = $hash;
        return $this;
    }
}
