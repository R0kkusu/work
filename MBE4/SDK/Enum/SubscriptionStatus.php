<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\Enum;

/**
 * Class SubscriptionStatus
 * @package Platform\Bundle\MBE\MBE4\SDK\Enum
 */
enum SubscriptionStatus: int
{
    case CREATED = 0;
    case ACTIVATED = 1;
    case TERMINATED = 2;
    case LOW_MONEY = 3;
}
