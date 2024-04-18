<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use Platform\Bundle\MBE\MBE4\SDK\Enum\TransactionStatus;

use function implode;
use function md5;

/**
 * Class NotificationRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class NotificationTransactionRequest extends NotificationRequest
{
    private string $transactionId;
    private string $clientTransactionId;

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getClientTransactionId(): string
    {
        return $this->clientTransactionId;
    }

    public function setClientTransactionId(string $clientTransactionId): self
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
            'transactionid' => $this->getTransactionId(),
            'clienttransactionid' => $this->getClientTransactionId(),
            'previousstatus' => $this->getPreviousStatus(),
            'status' => $this->getStatus(),
            'timestamp' => $this->getTimestamp()->format(BaseRequest::DATE_FORMAT)
        ];
    }

    public function isValid(string $password): bool
    {
        $computed = md5($password . implode('', $this->propertiesAsArray()));
        return $computed === $this->getHash();
    }

    public function isInFinalState(): bool
    {
        return $this->status === TransactionStatus::CAPTURED->value;
    }
}
