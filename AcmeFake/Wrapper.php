<?php

declare(strict_types=1);

namespace Platform\Bundle\ACME_Fake;

use Platform\Core\AbstractBillingBundle;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Repository\References\BillingChannel;
use Platform\Core\Repository\References\BillingKinematic;
use Platform\Core\Repository\References\CancellationChannel;
use Platform\Core\Repository\References\ExpirationPolicy;
use Platform\Core\Repository\References\NetworkChannel;
use Platform\Core\Repository\References\OrderChannel;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\SubscriptionHandler\DefaultBillingLogicHandlerTrait;
use Platform\Core\Transaction\Resolver\SessionHandler\SessionHandler;
use Platform\Core\Transaction\Resolver\TransactionContextDefinitionInterface;
use Platform\Core\Transaction\SubscriptionRegistrationContextInterface;
use Platform\Core\Transaction\TransactionInterface;

use function sprintf;
use function str_starts_with;

/**
 * Class Wrapper
 *
 * @package Platform\Bundle\ACME_Fake
 */
class Wrapper extends AbstractBillingBundle
{
    use DefaultBillingLogicHandlerTrait;

    public const Vodafone = 26202;
    public const O2 = 26207;

    private SDK\API $sdk;
    protected static array $codeMapping = [
        self::NOTIFICATION_CODE => [
            '2002' => [ProviderException::INTERNAL, 'Internal Error'],
            '2032' => [ProviderException::END_USER_NO_CREDIT, 'Subscriber has insufficient balance'],
            '4005' => [ProviderException::ACTION_REFUSED, 'Charging Error']
        ]
    ];

    public static function addCountryCode(int $mccmnc, ?string $msisdn): ?string
    {
        return match ($mccmnc) {
            self::Vodafone => sprintf('+49%s', $msisdn),
            self::O2 => sprintf('+49%s', $msisdn),
            default => null
        };
    }

    public function setSdk(SDK\API $sdk): Wrapper
    {
        $this->sdk = $sdk;
        return $this;
    }

    public function sdk(): SDK\API
    {
        return $this->sdk;
    }

    public static function getException(mixed $code, ?string $externalDetail = null, ?string $externalCode = null, string $group = self::API_CODE): ProviderException
    {
        [$code, $description] = self::translateCode($code, $group, $externalDetail);
        return (new ProviderException($code, $description))
            ->setExternalDetail($externalDetail)
            ->setExternalCode($externalCode);
    }

    protected function getSubscriptionInvoiceStrategy(TransactionInterface $transaction): int
    {
        return $transaction->getCountryId() === 'DE' ? self::INVOICE_NONE : $this->subscriptionInvoiceStrategy;
    }

    public function getSubscriptionRegistrationContext(TransactionRequest $request): ?SubscriptionRegistrationContextInterface
    {
        if ($request->getTransaction()->getCountryId() === 'DE') {
            return $this->createSubscriptionRegistrationContext()
                ->withProviderSubscriptionInactive()
                ->withChargingOnSubscribe()
                ->withoutSubscriptionAbortOnFailedCharge()
                ->withoutChargeMessage();
        }
        return null;
    }

    public function getExpirationPolicy(TransactionInterface $transaction): int
    {
        return $transaction->getCountryId() === 'DE' ? ExpirationPolicy::IMMEDIATE : $this->expirationPolicy;
    }

    // @formatter:off
    public function declareNotificationSessionHandler(SessionHandler $handler): void
    {
        $handler
            ->forType(TransactionInterface::TYPE_SUBSCRIPTION)
            ->activeSessionFromCache()
            ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
            ->endSession()
            ->activeSessionFromCache()
            ->withInput(TransactionContextDefinitionInterface::INPUT_PLATFORM_CORRELATION_ID)
            ->endSession()
            ->activeSessionFromDB()
            ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
            ->endSession()
            ->activeSessionFromDB()
            ->withInput(TransactionContextDefinitionInterface::INPUT_PLATFORM_CORRELATION_ID)
            ->endSession()
            ->activeSessionFromCache()
            ->withInput(TransactionContextDefinitionInterface::INPUT_MSISDN)
            ->withStatus(TransactionInterface::STATUS_PENDING)
            ->endSession()
            ->create()
            ->withDefaultBillingChannel(BillingChannel::MNO_BILLING)
            ->withDefaultKinematic(BillingKinematic::DIRECT)
            ->withDefaultOrderChannel(OrderChannel::SMS)
            ->withDefaultNetworkChannel(NetworkChannel::CELLULAR)
            ->end()
            ->endType();

        $handler->forType(TransactionInterface::TYPE_CANCELLATION)
            ->activeSessionFromCache()
            ->withInput(TransactionContextDefinitionInterface::INPUT_MSISDN)
            ->withStatus(TransactionInterface::STATUS_PENDING)
            ->endSession()
            ->passiveSessionFromCache()
            ->withInput(TransactionContextDefinitionInterface::INPUT_MSISDN)
            ->endSession()
            ->passiveSessionFromDB()
            ->withInput(TransactionContextDefinitionInterface::INPUT_MSISDN)
            ->endSession()
            ->endType();
    }
}
