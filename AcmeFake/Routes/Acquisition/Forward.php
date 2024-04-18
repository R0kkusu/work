<?php

declare(strict_types=1);

namespace Platform\Bundle\Routes\Acquisition;

use Platform\Bundle\AcmeFake\Wrapper;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Transaction\AcquisitionTransaction;
use Platform\Core\Transaction\FlowDependency;
use Platform\Router\AcquisitionStart\Route\Forward as BaseForward;
use Platform\Router\AcquisitionStart\Router as AcquisitionRouter;

/**
 * Class Forward
 * @package Platform\Bundle\Routes\Acquisition
 * @property Wrapper $bundle
 */
class Forward extends BaseForward
{
    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        parent::declareFlowDependency($dependency);

        $dependency
            ->withTypes([AcquisitionTransaction::TYPE_SUBSCRIPTION])
            ->withBillingChannels(BillingChannel::MNO_BILLING)
            ->withEndUserProviderIds($this->bundle->getSupportedEndUserProviderIds(), true);
    }

    protected function forward(
        AcquisitionRouter $router,
        AcquisitionTransaction $transaction,
        TransactionRequest $request
    ): TransactionAck {
        $parameters = [

            'clienttransactionid' => $transaction->getId(),
        ];

        $hashString = 'secretpassword';
        foreach ($parameters as $value) {
            $hashString .= $value;
        }
        $parameters['hash'] = md5($hashString);

        $forwardUrl = '{{consentUrl}}?' . http_build_query($parameters);

        return $this->suggestRedirect($request, $forwardUrl);
    }
}
