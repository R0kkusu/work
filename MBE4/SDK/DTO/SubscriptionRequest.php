<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class SubscriptionRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class SubscriptionRequest extends AuthorizationRequest
{
    private ?string $subscriptionId = null;
    private ?string $subscriptionDescription = null;
    private ?int $subscriptionInterval = null;

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): SubscriptionRequest
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getSubscriptionDescription(): ?string
    {
        return $this->subscriptionDescription;
    }

    public function setSubscriptionDescription(string $subscriptionDescription): SubscriptionRequest
    {
        $this->subscriptionDescription = $subscriptionDescription;
        return $this;
    }

    public function getSubscriptionInterval(): ?int
    {
        return $this->subscriptionInterval;
    }

    public function setSubscriptionInterval(int $subscriptionInterval): SubscriptionRequest
    {
        $this->subscriptionInterval = $subscriptionInterval;
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
            'callbackurl' => $this->getCallbackUrl(),
            'notificationurl' => $this->getNotificationUrl(),
            'subscriptionid' => $this->getSubscriptionId(),
            'subscriptiondescription' => $this->getSubscriptionDescription(),
            'subscriptioninterval' => $this->getSubscriptionInterval(),
            'timestamp' => $this->getTimestamp()->format(self::DATE_FORMAT),
            'mbe4pp_did' => $this->getLayoutId(),
        ];
    }
}
