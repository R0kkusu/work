<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Request;

use Asset\DataManipulation\CarbonDateTime;
use Platform\Bundle\MBE\MBE4\SDK\DTO\AuthorizationResponse;
use Platform\Bundle\MBE\MBE4\SDK\DTO\SubscriptionResponse;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Router\AcquisitionRelated\Request\TransactionCallbackRequest;
use UnexpectedValueException;

use function array_key_exists;
use function is_numeric;

/**
 * Class Callback
 * @package Platform\Bundle\MBE\MBE4\Request
 * @property Wrapper $bundle
 */
class Callback extends TransactionCallbackRequest
{
    protected function parseRequest(): void
    {
        $response = $this->getQueryParamsMap()->toArray();
        if (array_key_exists('subscriptionid', $response)) {
            //subscription
            /** @var SubscriptionResponse $carrierRequest */
            $carrierRequest = $this->bundle->getSdk()->getCarrierRequest($response, SubscriptionResponse::class);
            $this->setExternalSubscriptionId($carrierRequest->getSubscriptionId());
        } else {
            //oneshot
            /** @var AuthorizationResponse $carrierRequest */
            $carrierRequest = $this->bundle->getSdk()->getCarrierRequest($response, AuthorizationResponse::class);
            $this->setExternalInvoiceId($carrierRequest->getTransactionId());
        }

        if ($carrierRequest->getOperatorId() === '' && $carrierRequest->getResponseCode() === 0) {
            throw new UnexpectedValueException('operatorId is missing');
        }
        $this->setRelatedSessionId($carrierRequest->getClientTransactionId());

        if ($carrierRequest->getOperatorId() !== null && $carrierRequest->getOperatorId() !== '') {
            $this->setMobileCarrier(Wrapper::translateOperatorIdToMccmnc($carrierRequest->getOperatorId()), false);
            $this->setEventDate(CarbonDateTime::createFromTimestamp($carrierRequest->getTimestamp()->getTimestamp()));
            if (is_numeric($carrierRequest->getSubscriberId())) {
                $this->setMsisdn('+' . $carrierRequest->getSubscriberId(), false);
            } else {
                $this->setAliasProvider($carrierRequest->getSubscriberId(), false);
            }
        }
    }
}
