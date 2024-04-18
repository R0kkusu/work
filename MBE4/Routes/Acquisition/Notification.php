<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Routes\Acquisition;

use Asset\DataFormatter\UuidShortener;
use Asset\DataManipulation\CarbonDateTime;
use AtomSDK\AtomSdkHelper;
use Carbon\CarbonInterval;
use DateTime;
use Platform\Bundle\MBE\MBE4\Docs\Atom\SkuDatas;
use Platform\Bundle\MBE\MBE4\Request\Notification as NotificationRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionStatusRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\TransactionStatusRequest;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Repository\References\Period;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRouter;
use Platform\Core\Transaction\EditableTransactionInterface;
use Platform\Core\Transaction\FlowDependency;
use Platform\Core\Transaction\TransactionInterface;
use Platform\Router\AcquisitionRelated\Request\TransactionNotificationRequest;
use Platform\Router\AcquisitionRelated\Route\KinematicNotification;

use function is_numeric;
use function md5;
use function sleep;
use function sprintf;

/**
 * Class Notification
 * @package Platform\Bundle\MBE\MBE4\Routes\Acquisition
 * @property Wrapper $bundle
 */
class Notification extends KinematicNotification
{
    protected string $transactionRequestClass = NotificationRequest::class;

    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        $dependency
            ->withTypes([
                EditableTransactionInterface::TYPE_ONESHOT,
                EditableTransactionInterface::TYPE_SUBSCRIPTION,
                EditableTransactionInterface::TYPE_INVOICE,
                EditableTransactionInterface::TYPE_CANCELLATION,
                EditableTransactionInterface::TYPE_REFUND,
            ])
            ->withBillingChannels(BillingChannel::MNO_BILLING);
    }

    /**
     * @param NotificationRequest $request
     */
    protected function followSubscriptionNotification(
        TransactionRouter $router,
        TransactionNotificationRequest $request
    ): TransactionAck {

        $statusRequest = null;
        switch ($request->getEventType()) {
            case TransactionInterface::TYPE_CANCELLATION:
            case TransactionInterface::TYPE_REFUND:
                return $router->finalizeTransactionRequest($request);
            case TransactionInterface::TYPE_ONESHOT:
                $statusRequest = (new TransactionStatusRequest())
                    ->setClientTransactionId($request->getTransaction()->getId());
                break;
            case TransactionInterface::TYPE_SUBSCRIPTION:
                $statusRequest = (new SubscriptionStatusRequest())
                    ->setSubscriptionId(UuidShortener::toHex($request->getTransaction()->getId()));
                break;
            case TransactionInterface::TYPE_INVOICE:
                $statusRequest = (new SubscriptionStatusRequest())
                    ->setSubscriptionId(UuidShortener::toHex($request->getTransaction()->getRelatedId()));
                break;
        }

        if ($request->getTransaction()->getUserContext()->getMobileCarrier() === null && $statusRequest !== null) {
            $sku = $request->getSku();
            /** @var SkuDatas $skuDatas */
            $skuDatas = $sku->data;
            $retry = 5;
            do {
                $statusRequest
                    ->setServiceId($sku->external_id)
                    ->setUsername($skuDatas->username)
                    ->setPassword(md5($skuDatas->password))
                    ->setClientId($skuDatas->clientId)
                    ->setTimestamp(new DateTime());
                $response = $this->bundle->getSdk()->getStatus($statusRequest);
                $retry--;
                sleep(1);
            } while ($response->getStatus() !== $request->getCarrierRequest()->getStatus() && $retry > 0);

            if ($response->getResponsecode() === 0 && $response->getOperatorid() !== null) {
                $request->getTransaction()->getUserContext()->setMobileCarrier(Wrapper::translateOperatorIdToMccmnc($response->getOperatorid()));
                if (is_numeric($response->getSubscriberid())) {
                    $request->getTransaction()->getUserContext()->setMsisdn('+' . $response->getSubscriberid());
                } else {
                    $request->getTransaction()->getUserContext()->setAlias($response->getSubscriberid());
                }

                if ($request->getEventType() === TransactionInterface::TYPE_SUBSCRIPTION && !$request->hasSubscriptionTrialPeriod()) {
                    $cycleInterval = CarbonInterval::make(
                        AtomSdkHelper::iteratePeriod(Period::getIsoInterval($request->getOffer()->recurringperiod), $request->getOffer()->recurring_iteration)
                    );
                    $nextCycle = CarbonDateTime::now();
                    $nextCycle->add($cycleInterval);
                    $request->getApiTransaction()->setExternalNextInvoiceDate($nextCycle);
                }

                if ($request->getCarrierRequest()->isInFinalState()) {
                    return $router->finalizeTransactionRequest($request);
                }
            }
            $this->logger->warning(sprintf("Notification for transaction : %s is aborted a after 5 tries because transaction is not captured on provider side", $request->getTransaction()->getId()));
            return $this->ackKO($request, new ProviderException(ProviderException::GENERIC));
        }
        return $router->finalizeTransactionRequest($request);
    }
}
