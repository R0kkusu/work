<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use function md5;

/**
 * Class CaptureRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class CaptureRequest extends TransactionRequest
{
    protected string $do = TransactionRequest::DO_CAPTURE;
    private ?string $callbackUrl = null;
    private ?string $transactionId = null;

    public function getDo(): string
    {
        return $this->do;
    }

    public function setDo(string $do): CaptureRequest
    {
        $this->do = $do;
        return $this;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): CaptureRequest
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): CaptureRequest
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function propertiesAsArray(): array
    {
        return [
            'transactionid' => $this->getTransactionId(),
            'username' => $this->getUsername(),
            'clientid' => $this->getClientId(),
            'password' => md5($this->getPassword()),
            'do' => $this->getDo(),
            'callbackurl' => $this->getCallbackUrl(),
            'timestamp' => $this->getTimestamp(),
        ];
    }
}
