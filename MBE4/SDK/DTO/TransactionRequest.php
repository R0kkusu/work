<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\SDK\DTO;

/**
 * Class TransactionRequest
 * @package Platform\Bundle\MBE\MBE4\SDK\DTO
 */
abstract class TransactionRequest extends BaseRequest
{
    public const DO_CAPTURE = 'capture';
    public const DO_FOLLOWUP_DIRECT_CAPTURE = 'followupdirectcapture';

    protected string $do;

    public function getDo(): string
    {
        return $this->do;
    }
}
