<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Docs\Atom;

use AtomSDK\Objects\JsonObject;

/**
 * Class SkuDatas
 * @package Platform\Bundle\MBE\MBE4\Docs\Atom
 */
class SkuDatas extends JsonObject
{
    public string $clientId;
    public string $username;
    public string $password;
    public string $hashPassword;
    public string $service;
}
