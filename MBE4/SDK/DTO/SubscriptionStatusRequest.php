<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class SubscriptionStatusRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class SubscriptionStatusRequest extends BaseRequest
{
    protected string $do = 'subscriptionstatus';
    protected ?string $serviceId = null;
    protected ?string $subscriptionId = null;

    public function getDo(): string
    {
        return $this->do;
    }

    public function setDo(string $do): SubscriptionStatusRequest
    {
        $this->do = $do;
        return $this;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setServiceId(?string $serviceId): SubscriptionStatusRequest
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?string $subscriptionId): SubscriptionStatusRequest
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
             'username' => $this->getUsername(),
             'clientid' => $this->getClientId(),
             'password' => $this->getPassword(),
             'serviceid' => $this->getServiceId(),
             'do' => $this->getDo(),
             'subscriptionid' => $this->getSubscriptionId(),
             'timestamp' => $this->getTimestamp()->format(self::DATE_FORMAT),
         ];
    }
}
