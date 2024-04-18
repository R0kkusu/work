<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class AuthorizationRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class AuthorizationRequest extends BaseRequest
{
    private ?string $serviceId = null;
    private ?int $contentClass = null;
    private ?string $description = null;
    private ?string $clientTransactionId = null;
    private ?int $amount = null;
    private ?string $callbackUrl = null;
    private ?string $notificationUrl = null;
    private string $currency = 'EUR';
    private ?string $layoutId = null;
    protected array $excludedFormHash = ['mbe4pp_did'];

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): AuthorizationRequest
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getContentClass(): ?int
    {
        return $this->contentClass;
    }

    public function setContentClass(int $contentClass): AuthorizationRequest
    {
        $this->contentClass = $contentClass;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): AuthorizationRequest
    {
        $this->description = $description;
        return $this;
    }

    public function getClientTransactionId(): ?string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): AuthorizationRequest
    {
        $this->clientTransactionId = $clientTransactionId;
        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): AuthorizationRequest
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): AuthorizationRequest
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function getNotificationUrl(): ?string
    {
        return $this->notificationUrl;
    }

    public function setNotificationUrl(?string $notificationUrl): AuthorizationRequest
    {
        $this->notificationUrl = $notificationUrl;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): AuthorizationRequest
    {
        $this->currency = $currency;
        return $this;
    }

    public function getLayoutId(): ?string
    {
        return $this->layoutId;
    }

    public function setLayoutId(?string $layoutId): AuthorizationRequest
    {
        $this->layoutId = $layoutId;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'username' => $this->getUsername(),
            'clientid' => $this->getClientId(),
            'serviceid' => $this->getServiceId(),
            'contentclass' => $this->getContentClass(),
            'description' => $this->getDescription(),
            'clienttransactionid' => $this->getClientTransactionId(),
            'amount' => (string) $this->getAmount(),
            'currency' => $this->getCurrency(),
            'callbackurl' => $this->getCallbackUrl(),
            'notificationurl' => $this->getNotificationUrl(),
            'timestamp' => $this->getTimestamp()->format(self::DATE_FORMAT),
            'mbe4pp_did' => $this->getLayoutId(),
        ];
    }
}
