<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use DateTimeImmutable;

/**
 * Class StatusResponse
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
abstract class StatusResponse
{
    public int $responsecode;
    public string $description;
    public string $subscriberid;
    public ?string $operatorid = null;
    protected DateTimeImmutable $timestamp;

    abstract public function getStatus(): ?int;

    public function getResponsecode(): int
    {
        return $this->responsecode;
    }

    public function setResponsecode(int $responsecode): StatusResponse
    {
        $this->responsecode = $responsecode;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): StatusResponse
    {
        $this->description = $description;
        return $this;
    }

    public function getSubscriberid(): string
    {
        return $this->subscriberid;
    }

    public function setSubscriberid(string $subscriberid): StatusResponse
    {
        $this->subscriberid = $subscriberid;
        return $this;
    }

    public function getOperatorid(): ?string
    {
        return $this->operatorid;
    }

    public function setOperatorid(?string $operatorid): StatusResponse
    {
        $this->operatorid = $operatorid;
        return $this;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): StatusResponse
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
