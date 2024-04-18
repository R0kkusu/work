<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Routes\Internal;

use Asset\DataFormatter\UuidShortener;
use AtomSDK\AtomSdkHelper;
use AtomSDK\Resource\Product;
use Carbon\CarbonInterval;
use GuzzleHttp\Exception\ConnectException;
use Platform\Bundle\MBE\MBE4\Docs\Atom\SkuDatas;
use Platform\Bundle\MBE\MBE4\SDK\DTO\FollowupDirectCaptureRequest;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Repository\References\Period;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRouter;
use Platform\Core\Transaction\InvoiceTransaction;
use Platform\Router\AcquisitionRelated\Route\InvoiceSubscription as BaseInvoiceSubscription;
use Platform\Router\Internal\Request\InvoiceSubscriptionRequest;

use function is_null;
use function ltrim;
use function md5;
use function round;

/**
 * Class InvoiceSubscription
 * @package Platform\Bundle\MBE\MBE4\Routes\Internal
 * @property Wrapper $bundle
 */
class InvoiceSubscription extends BaseInvoiceSubscription
{
    protected function invoiceSubscription(TransactionRouter $router, InvoiceTransaction $invoiceTransaction, InvoiceSubscriptionRequest $request): TransactionAck
    {
        $subscriberId = is_null($invoiceTransaction->getUserContext()->getMsisdn()) ?
            $invoiceTransaction->getUserContext()->getAlias() :
            ltrim($invoiceTransaction->getUserContext()->getMsisdn(), '+');

        $sku = $request->getSku();
        $offer = $request->getOffer();
        /** @var Product $product */
        $product = $offer->getProduct();
        /** @var SkuDatas $skuData */
        $skuData = $sku->data;
        $followupDirectRequest = (new FollowupDirectCaptureRequest())
            ->setSubscriberId($subscriberId)
            ->setSubscriptionDescription($offer->commercial_name->value())
            ->setSubscriptionId(UuidShortener::toHex($invoiceTransaction->getSubscription()->getId()))
            ->setSubscriptionInterval((int) CarbonInterval::make(AtomSdkHelper::iteratePeriod(Period::getIsoInterval($offer->recurringperiod), $offer->recurring_iteration))->totalDays)
            ->setServiceId($sku->external_id)
            ->setContentClass($this->bundle->getContentClassFromThematic($product->thematic))
            ->setDescription($offer->commercial_name->value())
            ->setClientTransactionId($invoiceTransaction->getId())
            ->setAmount((int) round($offer->price * 100, 4))
            ->setTimestamp($invoiceTransaction->getInitDate()->toDateTimeImmutable())
            ->setUsername($skuData->username)
            ->setPassword(md5($skuData->password))
            ->setClientId($skuData->clientId)
        ;

        /** @var  FollowupDirectCaptureRequest $followupDirectRequest*/
        try {
            $response = $this->bundle->getSdk()->followupDirectCapture($followupDirectRequest);
        } catch (ConnectException) {
            return $router->finalizeTransactionRequest($request, new ProviderException(ProviderException::NETWORK));
        }

        $exception = null;
        if ($response->getResponseCode() !== 0) {
            [$code, $desc] = Wrapper::translateCode($request->getQueryParamsMap()->get('responsecode'));
            $exception = (new ProviderException($code, $desc))
                ->setExternalCode((string) $response->getResponseCode())
                ->setExternalDetail($response->getDescription());
        }
        return $router->finalizeTransactionRequest($request, $exception);
    }
}
