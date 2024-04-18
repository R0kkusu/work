<?php

declare(strict_types=1);

namespace Platform\Bundle\SDK\DTO;

class HttpForwardDTO
{
    private string $consentUrl;
    private string $username;
    private string $clientId;
    private string $serviceId;
    private string $contentClass;
    private string $description;
    private string $clientTransactionId;
    private string $amount;
    private string $callbackUrl;
    private string $subscriptionId;
    private string $subscriptionDescription;
    private string $subscriptionInterval;
    private string $timestamp;
    private string $hash;

    public function getConsentUrl(): string
    {
        return $this->consentUrl;
    }

    public function setConsentUrl(string $consentUrl): HttpForwardDTO
    {
        $this->consentUrl = $consentUrl;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): HttpForwardDTO
    {
        $this->username = $username;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): HttpForwardDTO
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): HttpForwardDTO
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getContentClass(): string
    {
        return $this->contentClass;
    }

    public function setContentClass(string $contentClass): HttpForwardDTO
    {
        $this->contentClass = $contentClass;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): HttpForwardDTO
    {
        $this->description = $description;
        return $this;
    }

    public function getClientTransactionId(): string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): HttpForwardDTO
    {
        $this->clientTransactionId = $clientTransactionId;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): HttpForwardDTO
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): HttpForwardDTO
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): HttpForwardDTO
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getSubscriptionDescription(): string
    {
        return $this->subscriptionDescription;
    }

    public function setSubscriptionDescription(string $subscriptionDescription): HttpForwardDTO
    {
        $this->subscriptionDescription = $subscriptionDescription;
        return $this;
    }

    public function getSubscriptionInterval(): string
    {
        return $this->subscriptionInterval;
    }

    public function setSubscriptionInterval(string $subscriptionInterval): HttpForwardDTO
    {
        $this->subscriptionInterval = $subscriptionInterval;
        return $this;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): HttpForwardDTO
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): HttpForwardDTO
    {
        $this->hash = $hash;
        return $this;
    }
}
