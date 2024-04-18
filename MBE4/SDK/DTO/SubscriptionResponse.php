<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class SubscriptionResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class SubscriptionResponse extends AuthorizationResponse
{
    private string $subscriptionId;

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): SubscriptionResponse
    {
        $this->subscriptionId = $subscriptionId;
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
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT),
            'subscriptionid' => $this->getSubscriptionId(),
        ];
    }
}
