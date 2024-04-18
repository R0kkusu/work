<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use DateTimeImmutable;

/**
 * Class SubscriptionStatusResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class SubscriptionStatusResponse extends StatusResponse
{
    public ?int $subscriptionStatus = null;
    public DateTimeImmutable $lastBilling;

    public function getStatus(): ?int
    {
        return $this->getSubscriptionStatus();
    }

    public function getSubscriptionStatus(): ?int
    {
        return $this->subscriptionStatus;
    }

    public function setSubscriptionStatus(int $subscriptionStatus): SubscriptionStatusResponse
    {
        $this->subscriptionStatus = $subscriptionStatus;
        return $this;
    }

    public function getLastBilling(): DateTimeImmutable
    {
        return $this->lastBilling;
    }

    public function setLastBilling(DateTimeImmutable $lastBilling): SubscriptionStatusResponse
    {
        $this->lastBilling = $lastBilling;
        return $this;
    }
}
