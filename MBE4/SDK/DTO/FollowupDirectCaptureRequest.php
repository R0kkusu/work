<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class FollowupDirectCaptureRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class FollowupDirectCaptureRequest extends SubscriptionRequest
{
    protected string $do = 'followupdirectcapture';
    protected ?string $subscriberId = null;
    protected string $orderType = 'web';

    public function getDo(): string
    {
        return $this->do;
    }

    public function getSubscriberId(): ?string
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(?string $subscriberId): FollowupDirectCaptureRequest
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getOrderType(): string
    {
        return $this->orderType;
    }

    public function setOrderType(string $orderType): FollowupDirectCaptureRequest
    {
        $this->orderType = $orderType;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'subscriptionid' => $this->getSubscriptionId(),
            'subscriptiondescription' => $this->getSubscriptionDescription(),
            'subscriptioninterval' => $this->getSubscriptionInterval(),
            'username' => $this->getUsername(),
            'clientid' => $this->getClientId(),
            'password' => $this->getPassword(),
            'serviceid' => $this->getServiceId(),
            'contentclass' => $this->getContentClass(),
            'description' => $this->getDescription(),
            'clienttransactionid' => $this->getClientTransactionId(),
            'amount' => $this->getAmount(),
            'subscriberid' => $this->getSubscriberId(),
            'do' => $this->getDo(),
            'callbackurl' => $this->getCallbackUrl(),
            'ordertype' => $this->getOrderType(),
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT),
        ];
    }
}
