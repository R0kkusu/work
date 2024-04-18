<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class StatusRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class TransactionStatusRequest extends BaseRequest
{
    protected string $do = 'status';
    protected ?string $serviceId = null;
    protected ?string $clientTransactionId = null;

    public function getDo(): string
    {
        return $this->do;
    }

    public function setDo(string $do): TransactionStatusRequest
    {
        $this->do = $do;
        return $this;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setServiceId(?string $serviceId): TransactionStatusRequest
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getClientTransactionId(): ?string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(?string $clientTransactionId): TransactionStatusRequest
    {
        $this->clientTransactionId = $clientTransactionId;
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
            'clienttransactionid' => $this->getClientTransactionId(),
            'serviceid' => $this->getServiceId(),
            'do' => $this->getDo(),
            'timestamp' => $this->getTimestamp()->format(self::DATE_FORMAT),
        ];
    }
}
