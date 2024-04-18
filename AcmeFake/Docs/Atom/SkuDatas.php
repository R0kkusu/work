<?php

declare(strict_types=1);

namespace Platform\Bundle\MTN\MADAPI\Docs\Atom;

use AtomSDK\Objects\JsonObject;

/**
 * Class SkuDatas
 * @package Platform\Bundle\MTN\MADAPI\Docs\Atom
 */
class SkuDatas extends JsonObject
{
    public string $nodeId;
    public string $subscriptionProviderId;
    public ?string $apiKey;
    public string $serviceId;
    public string $subscriptionDescription;
    public ?string $clientId;
    public ?string $clientSecret;
    public string $registrationChannel;
}
