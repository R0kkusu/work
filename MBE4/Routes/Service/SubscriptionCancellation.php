<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Routes\Service;

use Asset\DataFormatter\UuidShortener;
use GuzzleHttp\Exception\ConnectException;
use Platform\Bundle\MBE\MBE4\Docs\Atom\SkuDatas;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionTerminateRequest;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Transaction\SubscriptionCancellationTransaction;
use Platform\Router\AcquisitionStart\Route\SubscriptionCancellation as BaseSubscriptionCancellation;
use Platform\Router\AcquisitionStart\Router as AcquisitionTransactionRouter;

use function is_null;
use function ltrim;
use function md5;

/**
 * Class SubscriptionCancellation
 * @package Platform\Bundle\MBE\MBE4\Routes\Service
 * @property Wrapper $bundle
 */
class SubscriptionCancellation extends BaseSubscriptionCancellation
{
    protected int $cancellationKinematic = SubscriptionCancellationTransaction::KINEMATIC_DIRECT;

    protected function cancelSubscription(AcquisitionTransactionRouter $router, SubscriptionCancellationTransaction $transaction, TransactionRequest $request): TransactionAck
    {
        $sku = $request->getSku();
        /** @var SkuDatas $skuData */
        $skuData = $sku->data;
        $subscriberId = is_null($transaction->getUserContext()->getMsisdn()) ?
            $transaction->getUserContext()->getAlias() :
            ltrim($transaction->getUserContext()->getMsisdn(), '+');
        /** @var SubscriptionTerminateRequest $terminateRequest */
        $terminateRequest = (new SubscriptionTerminateRequest())
            ->setReason('service')
            ->setSubscriberId($subscriberId)
            ->setServiceId($sku->external_id)
            ->setClientTransactionId($transaction->getId())
            ->setTimestamp($transaction->getInitDate()->toDateTimeImmutable())
            ->setUsername($skuData->username)
            ->setPassword(md5($skuData->password))
            ->setClientId($skuData->clientId)
            ;
        $terminateRequest->setSubscriptionId(UuidShortener::toHex($transaction->getSubscription()->getId()));
        try {
            $response = $this->bundle->getSdk()->subscriptionTerminate($terminateRequest);
        } catch (ConnectException) {
            return $router->finalizeTransactionRequest($request, new ProviderException(ProviderException::NETWORK));
        }

        $exception = null;
        if ($response->getResponseCode() !== 0) {
            [$code, $desc] = Wrapper::translateCode($response->getResponseCode());
            $exception = (new ProviderException($code, $desc))
                ->setExternalCode((string) $response->getResponseCode())
                ->setExternalDetail($response->getDescription());
        }
        return $router->finalizeTransactionRequest($request, $exception);
    }
}
