<?php

declare(strict_types=1);

namespace Platform\Bundle\Routes\Acquisition;

use Asset\DataManipulation\CarbonDateTime;
use Platform\Bundle\Vodafone\GIG\Request\Notification as NotificationRequest;
use Platform\Core\Exception\InternalException;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRouter;
use Platform\Core\Transaction\EditableTransactionInterface;
use Platform\Core\Transaction\FlowDependency;
use Platform\Router\AcquisitionRelated\Request\TransactionNotificationRequest;
use Platform\Router\AcquisitionRelated\Route\KinematicNotification;

use function substr;

/**
 * Class Notification
 * @package Platform\Bundle\Routes\Acquisition
 */
class Notification extends KinematicNotification
{
    protected string $transactionRequestClass = NotificationRequest::class;

    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        parent::declareFlowDependency($dependency);

        // Context requirements
        $dependency
            ->withTypes([
                EditableTransactionInterface::TYPE_SUBSCRIPTION,
                EditableTransactionInterface::TYPE_CANCELLATION,

            ])
            ->withBillingChannels(BillingChannel::MNO_BILLING);
    }

    protected function followSubscriptionNotification(TransactionRouter $router, TransactionNotificationRequest $request): TransactionAck
    {
        /** @var NotificationRequest $request */
        switch ($request->getEventType()) {
            case EditableTransactionInterface::TYPE_SUBSCRIPTION:
                $request->getTransaction()->getUserContext()->setAliasBp(
                    substr($request->header->getCustomerId()->getIdentifier(), 33)
                );
                break;

            case EditableTransactionInterface::TYPE_CANCELLATION:
                $subscriptionEndDateTime = $request
                    ->updateNotification->getSubscriptionData()->getSubscriptionParameter('subscriptionEndDateTime');
                if (isset($subscriptionEndDateTime)) {
                    $request
                        ->getApiTransaction()->setExternalExpirationDate(new CarbonDateTime($subscriptionEndDateTime));
                }
                break;
        }
        return $router->finalizeTransactionRequest($request);
    }
}
