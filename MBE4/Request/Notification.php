<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Request;

use Asset\DataFormatter\UuidShortener;
use Platform\Bundle\MBE\MBE4\SDK\DTO\NotificationSubscriptionRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\NotificationTransactionRequest;
use Platform\Bundle\MBE\MBE4\SDK\Enum\SubscriptionStatus;
use Platform\Bundle\MBE\MBE4\SDK\Enum\TransactionStatus;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\NotSupportedException;
use Platform\Core\Transaction\TransactionInterface;
use Platform\Router\AcquisitionRelated\Request\TransactionNotificationRequest;

use function array_key_exists;

/**
 * Class Callback
 * @package Platform\Bundle\MBE\MBE4\Request
 * @property Wrapper $bundle
 */
class Notification extends TransactionNotificationRequest
{
    private NotificationTransactionRequest|NotificationSubscriptionRequest $carrierRequest;

    protected function parseRequest(): void
    {

        $queryParams = $this->getQueryParamsMap()->toArray();
        $isSubNotification = array_key_exists('subscriptionid', $queryParams);
        $notifClass = $isSubNotification ? NotificationSubscriptionRequest::class : NotificationTransactionRequest::class;
        $this->carrierRequest = $this->bundle->getSdk()->getCarrierRequest($queryParams, $notifClass);

        switch ($this->getPathLevel(0)) {
            case 'subscription':
                if ($isSubNotification) {
                    if ($this->carrierRequest->getStatus() === SubscriptionStatus::TERMINATED->value) {
                        $this->setEventType(TransactionInterface::TYPE_CANCELLATION);
                        $this->setSubscriptionId(UuidShortener::fromHex($this->carrierRequest->getClientSubscriptionId()));
                    } else {
                        $this->setEventType(TransactionInterface::TYPE_SUBSCRIPTION);
                        $this->setSubscriptionId(UuidShortener::fromHex($this->carrierRequest->getClientSubscriptionId()));
                        $this->setSessionId(UuidShortener::fromHex($this->carrierRequest->getClientSubscriptionId()));
                    }
                } else {
                    if ($this->carrierRequest->getStatus() === TransactionStatus::REFUNDED->value) {
                        $this->setEventType(TransactionInterface::TYPE_REFUND);
                    } else {
                        $this->setEventType(TransactionInterface::TYPE_INVOICE);
                        $this->setSubscriptionId($this->carrierRequest->getClientTransactionId());
                        $this->setRelatedId($this->carrierRequest->getClientTransactionId());
                    }
                    $this->setExternalInvoiceId($this->carrierRequest->getTransactionId());
                }
                break;
            case 'oneshot':
                $this->setEventType(TransactionInterface::TYPE_ONESHOT);
                $this->setSessionId($this->carrierRequest->getClientTransactionId());
                $this->setExternalInvoiceId($this->carrierRequest->getTransactionId());
                break;
            default:
                throw new NotSupportedException('Unsupported notification');
        }
    }

    public function getCarrierRequest(): NotificationTransactionRequest| NotificationSubscriptionRequest
    {
        return $this->carrierRequest;
    }
}
