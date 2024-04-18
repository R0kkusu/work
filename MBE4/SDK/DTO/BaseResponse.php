<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use DateTimeImmutable;

/**
 * Class BaseResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
class BaseResponse
{
    protected int $responseCode;
    protected string $description;
    protected string $transactionId;
    protected DateTimeImmutable $timestamp;

    public function setResponseCode(int $responseCode): BaseResponse
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function isSuccess(): bool
    {
        return $this->getResponseCode() === 0;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setDescription(string $description): BaseResponse
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTransactionId(string $transactionId): BaseResponse
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): BaseResponse
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
