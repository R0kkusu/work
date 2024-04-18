<?php

declare(strict_types=1);

namespace Platform\Bundle\AcmeFake\Routes\Acquisition;

use Platform\Bundle\AcmeFake\Request\Callback as BaseCallback;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Routing\TransactionRouter;
use Platform\Core\Transaction\AcquisitionTransaction;
use Platform\Core\Transaction\ApiTransaction;
use Platform\Core\Transaction\FlowDependency;
use Platform\Router\AcquisitionRelated\Route\EndUserCallback;

class Callback extends EndUserCallback
{
    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        $dependency
            ->withTypes([AcquisitionTransaction::TYPE_SUBSCRIPTION])
            ->withBillingChannels(BillingChannel::MNO_BILLING)
            ->withEndUserProviderIds($this->bundle->getSupportedEndUserProviderIds());
    }

    /**
     * @param BaseCallback $request
     */
    protected function followKinematicCallback(
        TransactionRouter $router,
        ApiTransaction $transaction,
        TransactionRequest $request
    ): TransactionAck {
        if ($request->getQueryParamsMap()->get('status') === '0') {
            return $this->ackKO($request, new ProviderException(ProviderException::GENERIC));
        }
        return $this->ackOK($request);
    }
}
