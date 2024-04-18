<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

use DateTimeInterface;

use function array_diff_key;
use function array_flip;
use function implode;
use function md5;

/**
 * Class BaseRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
abstract class BaseRequest
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    protected ?string $password = null;
    protected ?string $username = null;
    protected ?string $clientId = null;
    protected ?DateTimeInterface $timestamp = null;
    protected array $excludedFormHash = [];

    /**
     * @return string[]
     */
    abstract protected function propertiesAsArray(): array;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): BaseRequest
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): BaseRequest
    {
        $this->password = $password;
        return $this;
    }

    public function setTimestamp(DateTimeInterface $timestamp): BaseRequest
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setClientId(string $clientId): BaseRequest
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @return string[]
     */
    public function asArray(): array
    {
        $properties = $this->propertiesAsArray();
        $propertiesForHash = array_diff_key($properties, array_flip($this->excludedFormHash));
        $properties['hash'] = md5($this->getPassword() . implode('', $propertiesForHash));
        return $properties;
    }
}
