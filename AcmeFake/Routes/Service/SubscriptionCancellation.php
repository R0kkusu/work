<?php

declare(strict_types=1);

namespace Platform\Bundle\Routes\Service;

use GuzzleHttp\Exception\ConnectException;
use Platform\Bundle\Wrapper;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Transaction\SubscriptionCancellationTransaction;
use Platform\Router\AcquisitionStart\Route\SubscriptionCancellation as BaseSubscriptionCancellation;
use Platform\Router\AcquisitionStart\Router as AcquisitionTransactionRouter;

use function ltrim;
use function trim;

/**
 * Class SubscriptionCancellation
 * @package Platform\Bundle\Routes\Acquisition
 * @property Wrapper $bundle
 */
class SubscriptionCancellation extends BaseSubscriptionCancellation
{
    protected int $cancellationKinematic = SubscriptionCancellationTransaction::KINEMATIC_DIRECT;

    protected function cancelSubscription(AcquisitionTransactionRouter $router, SubscriptionCancellationTransaction $transaction, TransactionRequest $request): TransactionAck
    {
        try {
            $sku = $request->getSku();
            $response = $this->bundle->sdk()->exit(
                $sku->external_id,
                ltrim($transaction->getUserContext()->getMsisdn(), '+')
            );
        } catch (ConnectException $e) {
            return $router->finalizeTransactionRequest($request, new ProviderException(ProviderException::NETWORK));
        }

        if ($response->getStatusCode() === 200) {
            return $router->finalizeTransactionRequest($request);
        }

        [$code, $desc] = Wrapper::translateCode($body = trim((string) $response->getBody()));
        $exception = new ProviderException($code, $desc);
        $exception->setExternalCode($body);
        return $router->finalizeTransactionRequest($request, $exception);
    }
}
