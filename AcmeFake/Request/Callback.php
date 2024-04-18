<?php

declare(strict_types=1);

namespace Platform\Bundle\AcmeFake\Request;

use Platform\Bundle\AcmeFake\Wrapper;
use Platform\Router\AcquisitionRelated\Request\TransactionCallbackRequest;

use function uuid_is_valid;

/**
 * Class Callback
 * @package Bundle\Request
 * @property Wrapper $bundle
 */
class Callback extends TransactionCallbackRequest
{
    protected function parseRequest(): void
    {
        $sessionId = $this->getQueryParamsMap()->get('tid');
        if (empty($sessionId)) {
            throw new MissingInputException(MissingInputException::OPERATION_ID);
        }
        $this->setRelatedSessionId($sessionId);
    }
}
