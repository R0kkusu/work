<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use DateTimeImmutable;

/**
 * Class NotificationRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
abstract class NotificationRequest
{
    protected string $hash;
    protected int $previousStatus;
    protected int $status;
    protected DateTimeImmutable $timestamp;

    /**
     * @return string[]
     */
    abstract protected function propertiesAsArray(): array;

    abstract public function isInFinalState(): bool;

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): NotificationRequest
    {
        $this->hash = $hash;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setStatus(int $status): NotificationRequest
    {
        $this->status = $status;
        return $this;
    }

    public function setPreviousStatus(int $previousStatus): NotificationRequest
    {
        $this->previousStatus = $previousStatus;
        return $this;
    }

    public function getPreviousStatus(): int
    {
        return $this->previousStatus;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): NotificationRequest
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
