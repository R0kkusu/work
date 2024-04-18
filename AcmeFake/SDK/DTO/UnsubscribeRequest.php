<?php

declare(strict_types=1);

namespace Platform\Bundle\AcmeFake\SDK\DTO;

class SubscriptionTerminateRequestDTO
{
    private string $billingUrl;
    private string $subscriptionId;
    private string $username;
    private string $clientId;
    private string $password;
    private string $serviceId;
    private string $clientTransactionId;
    private string $reason;
    private string $subscriberId;
    private string $callbackUrl;
    private string $timestamp;

    public function getBillingUrl(): string
    {
        return $this->billingUrl;
    }

    public function setBillingUrl(string $billingUrl): SubscriptionTerminateRequestDTO
    {
        $this->billingUrl = $billingUrl;
        return $this;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): SubscriptionTerminateRequestDTO
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): SubscriptionTerminateRequestDTO
    {
        $this->username = $username;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): SubscriptionTerminateRequestDTO
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): SubscriptionTerminateRequestDTO
    {
        $this->password = $password;
        return $this;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): SubscriptionTerminateRequestDTO
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getClientTransactionId(): string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): SubscriptionTerminateRequestDTO
    {
        $this->clientTransactionId = $clientTransactionId;
        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): SubscriptionTerminateRequestDTO
    {
        $this->reason = $reason;
        return $this;
    }

    public function getSubscriberId(): string
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(string $subscriberId): SubscriptionTerminateRequestDTO
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): SubscriptionTerminateRequestDTO
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): SubscriptionTerminateRequestDTO
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
