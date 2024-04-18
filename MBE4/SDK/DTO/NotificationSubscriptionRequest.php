<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use Platform\Bundle\MBE\MBE4\SDK\Enum\SubscriptionStatus;

/**
 * Class NotificationSubscriptionRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class NotificationSubscriptionRequest extends NotificationRequest
{
    private string $subscriptionId;
    private string $clientSubscriptionId;

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): NotificationSubscriptionRequest
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getClientSubscriptionId(): string
    {
        return $this->clientSubscriptionId;
    }

    public function setClientSubscriptionId(string $clientSubscriptionId): NotificationSubscriptionRequest
    {
        $this->clientSubscriptionId = $clientSubscriptionId;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'subscriptionid' => $this->getSubscriptionId(),
            'clientsubscriptionid' => $this->getClientSubscriptionId(),
            'previousstatus' => $this->getPreviousStatus(),
            'status' => $this->getStatus(),
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT)
        ];
    }

    public function isInFinalState(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVATED->value;
    }
}
