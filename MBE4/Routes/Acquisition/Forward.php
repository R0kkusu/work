<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Routes\Acquisition;

use Asset\DataFormatter\UuidShortener;
use AtomSDK\AtomSdkHelper;
use AtomSDK\Resource\Product;
use Carbon\CarbonInterval;
use Platform\Bundle\MBE\MBE4\Docs\Atom\SkuDatas;
use Platform\Bundle\MBE\MBE4\SDK\DTO\AuthorizationRequest;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionRequest;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Application;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Repository\References\Period;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Transaction\AcquisitionTransaction;
use Platform\Core\Transaction\FlowDependency;
use Platform\Core\Transaction\TransactionInterface;
use Platform\Router\AcquisitionStart\Route\Forward as BaseForward;
use Platform\Router\AcquisitionStart\Router as AcquisitionRouter;

use function round;
use function sprintf;

/**
 * @property Wrapper $bundle
 */
class Forward extends BaseForward
{
    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        parent::declareFlowDependency($dependency);

        $dependency
            ->withTypes([AcquisitionTransaction::TYPE_ONESHOT, AcquisitionTransaction::TYPE_SUBSCRIPTION])
            ->withBillingChannels(BillingChannel::MNO_BILLING);
    }

    protected function forward(
        AcquisitionRouter $router,
        AcquisitionTransaction $transaction,
        TransactionRequest $request
    ): TransactionAck {
        $sku = $request->getSku();
        $offer = $request->getOffer();
        /** @var Product $product */
        $product = $offer->getProduct();
        /** @var SkuDatas $skuData */
        $skuData = $sku->data;
        $isOneshot = false;

        $notifUrl = sprintf('https://%s%s', Application::getHost(), $this->getNotificationURI());
        //oneshot
        if ($transaction->getType() === TransactionInterface::TYPE_ONESHOT) {
            $forwardRequest = (new AuthorizationRequest());
            $isOneshot = true;
            $notifUrl = sprintf('%s/oneshot', $notifUrl);
        } else {
            //subscription
            $forwardRequest = (new SubscriptionRequest())
                ->setSubscriptionDescription($offer->commercial_name->value())
                ->setSubscriptionId(UuidShortener::toHex($transaction->getId()))
                ->setSubscriptionInterval((int) CarbonInterval::make(AtomSdkHelper::iteratePeriod(Period::getIsoInterval($offer->recurringperiod), $offer->recurring_iteration))->totalDays);
            $notifUrl = sprintf('%s/subscription', $notifUrl);
        }
        $forwardRequest->setLayoutId($transaction->getUserContext()->getLayoutId())
            ->setServiceId($sku->external_id)
            ->setContentClass($this->bundle->getContentClassFromThematic($product->thematic))
            ->setDescription($offer->commercial_name->value())
            ->setClientTransactionId($transaction->getId())
            ->setAmount((int) round($offer->price * 100, 4))
            ->setCurrency($offer->currency)
            ->setCallbackUrl(sprintf('https://%s%s', Application::getHost(), $this->getCallbackURI()))
            ->setNotificationUrl($notifUrl)
            ->setTimestamp($transaction->getInitDate())
            ->setUsername($skuData->username)
            ->setPassword($skuData->hashPassword)
            ->setClientId($skuData->clientId);
        $redirectUrl = $this->bundle->getSdk()->generateForwardUrl($forwardRequest, $isOneshot);
        return $this->suggestRedirect($request, $redirectUrl);
    }
}
