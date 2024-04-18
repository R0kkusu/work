<?php

declare(strict_types=1);

namespace Platform\Bundle\Request;

use Asset\DataManipulation\CarbonDateTime;
use DateTimeZone;
use Platform\Bundle\AcmeFake\Wrapper;
use Platform\Core\Exception\NotSupportedException;
use Platform\Core\Transaction\EditableTransactionInterface;
use Platform\Router\AcquisitionRelated\Request\TransactionNotificationRequest;

use function in_array;
use function is_null;
use function sprintf;
use function str_replace;

/**
 * Class Notification
 * @package Platform\Bundle\NTH\MPG\Request
 * @property Wrapper $bundle
 */
class notification extends TransactionNotificationRequest
{
    protected function parseRequest(): void
    {

        $map = $this->getQueryParamsMap();
        if (!$map->hasKey('status')) {
            $this
                ->setEventType(EditableTransactionInterface::TYPE_SUBSCRIPTION)
                ->setSessionId($this->getQueryParamsMap()->get('subscribtionId', null));
        } else {
            if ($this->getBodyParamsMap()->get('status', null) === '0') {
                $this
                    ->setEventType(EditableTransactionInterface::TYPE_SUBSCRIPTION)
                    ->setSessionId($this->getQueryParamsMap()->get('transactionId', null));
            } elseif ($this->getBodyParamsMap()->get('status', null) === '1') {
                $this
                    ->setEventType(EditableTransactionInterface::TYPE_CANCELLATION)
                    ->setSubscriptionId($this->getQueryParamsMap()->get('transactionId', null));
            } else {
                throw new NotSupportedException(sprintf('Unsupported event \'%s\'', $this->getBodyParamsMap()->get('status', null)));
            }
            $this
                ->setSkuExternalId($this->getBodyParamsMap()->get('skuId', null))
                ->setPlatformCorrelationId($this->getBodyParamsMap()->get('transactionId', null))
                ->setExternalSubscriptionId($this->getBodyParamsMap()->get('subscriptionId', null));
        }
    }
}
