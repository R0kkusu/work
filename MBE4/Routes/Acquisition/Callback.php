<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Routes\Acquisition;

use Asset\StateRouter\Route\RequestAttributeFilter;
use Platform\Bundle\MBE\MBE4\Request\Callback as CallbackRequest;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Routing\TransactionAck;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\Routing\TransactionRouter;
use Platform\Core\Transaction\AcquisitionTransaction;
use Platform\Core\Transaction\ApiTransaction;
use Platform\Core\Transaction\FlowDependency;
use Platform\Router\AcquisitionRelated\Route\EndUserCallback;

use function str_contains;

/**
 * Class Callback
 * @package Platform\Bundle\MBE\MBE4\Routes\Acquisition
 * @property Wrapper $bundle
 */
class Callback extends EndUserCallback
{
    protected string $transactionRequestClass = CallbackRequest::class;

    public function __construct()
    {
        $this->withRequestInput('transactionid', RequestAttributeFilter::STRING);
        $this->withRequestInput('clienttransactionid', RequestAttributeFilter::STRING);
        $this->withRequestInput('responsecode', RequestAttributeFilter::NUMERIC);
        $this->withRequestInput('description', RequestAttributeFilter::STRING);
        $this->withRequestInput('subscriberid', RequestAttributeFilter::STRING);
        $this->withRequestInput('timestamp', RequestAttributeFilter::STRING);
        $this->withRequestInput('operatorid', RequestAttributeFilter::STRING);
        $this->withRequestInput('hash', RequestAttributeFilter::STRING);
    }

    protected function declareFlowDependency(FlowDependency $dependency): void
    {
        $dependency
            ->withTypes([AcquisitionTransaction::TYPE_ONESHOT, AcquisitionTransaction::TYPE_SUBSCRIPTION])
            ->withBillingChannels(BillingChannel::MNO_BILLING);
    }

    /**
     * @param CallbackRequest $request
     */
    protected function followKinematicCallback(
        TransactionRouter $router,
        ApiTransaction $transaction,
        TransactionRequest $request
    ): TransactionAck {
        if ($request->getQueryParamsMap()->get('responsecode') === '0') {
            return $this->ackOK($request);
        }

        [$code, $desc] = Wrapper::translateCode($request->getQueryParamsMap()->get('responsecode'));
        $exception = new ProviderException($code, $desc);

        if (str_contains($request->getQueryParamsMap()->get('description'), 'LOW_MONEY')) {
            $exception = new ProviderException(ProviderException::END_USER_NO_CREDIT);
        }
        $exception->setExternalDetail($request->getQueryParamsMap()->get('description'));
        $exception->setExternalCode($request->getQueryParamsMap()->get('responsecode'));
        return $router->finalizeTransactionRequest($request, $exception);
    }
}
