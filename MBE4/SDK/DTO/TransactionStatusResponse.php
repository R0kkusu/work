<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class CaptureResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class TransactionStatusResponse extends StatusResponse
{
    public ?int $status = null;
    public string $transactionid;
    public string $clienttransactionid;

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): TransactionStatusResponse
    {
        $this->status = $status;
        return $this;
    }

    public function getTransactionid(): string
    {
        return $this->transactionid;
    }

    public function setTransactionid(string $transactionid): TransactionStatusResponse
    {
        $this->transactionid = $transactionid;
        return $this;
    }

    public function getClientransactionid(): string
    {
        return $this->clienttransactionid;
    }

    public function setClienttransactionid(string $clienttransactionid): TransactionStatusResponse
    {
        $this->clienttransactionid = $clienttransactionid;
        return $this;
    }
}
