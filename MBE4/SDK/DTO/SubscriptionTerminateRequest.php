<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class SubscriptionTerminateRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class SubscriptionTerminateRequest extends SubscriptionRequest
{
    protected string $do = 'subscriptionterminate';
    protected ?string $subscriberId = null;
    protected string $orderType = 'web';
    protected ?string $reason = null;

    public function getDo(): string
    {
        return $this->do;
    }

    public function getSubscriberId(): ?string
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(?string $subscriberId): SubscriptionRequest
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getOrderType(): string
    {
        return $this->orderType;
    }

    public function setOrderType(string $orderType): SubscriptionRequest
    {
        $this->orderType = $orderType;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): SubscriptionTerminateRequest
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'subscriptionid' => $this->getSubscriptionId(),
            'username' => $this->getUsername(),
            'clientid' => $this->getClientId(),
            'password' => $this->getPassword(),
            'serviceid' => $this->getServiceId(),
            'clienttransactionid' => $this->getClientTransactionId(),
            'reason' => $this->getReason(),
            'subscriberid' => $this->getSubscriberId(),
            'do' => $this->getDo(),
            'callbackurl' => $this->getCallbackUrl(),
            'ordertype' => $this->getOrderType(),
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT),
        ];
    }
}
