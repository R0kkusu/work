<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4;

use InvalidArgumentException;
use Platform\Bundle\MBE\MBE4\SDK\API;
use Platform\Bundle\MBE\MBE4\SDK\DTO\ContentClass;
use Platform\Core\AbstractBillingBundle;
use Platform\Core\Exception\ProviderException;
use Platform\Core\Repository\References\Thematic;
use Platform\Core\Routing\TransactionRequest;
use Platform\Core\SubscriptionHandler\DefaultBillingLogicHandlerTrait;
use Platform\Core\Transaction\Resolver\SessionHandler\SessionHandler;
use Platform\Core\Transaction\Resolver\TransactionContextDefinitionInterface;
use Platform\Core\Transaction\SubscriptionRegistrationContextInterface;
use Platform\Core\Transaction\TransactionInterface;

use function sprintf;

/**
 * Class Wrapper
 * @package Platform\Bundle\MBE\MBE4
 */
class Wrapper extends AbstractBillingBundle
{
    use DefaultBillingLogicHandlerTrait;

    private API $sdk;
    protected static array $codeMapping = [
        self::API_CODE => [
            '2' => [ProviderException::SETTINGS, 'Authorization failed.'],
            '3' => [ProviderException::INTERNAL, 'Capture failed.'],
            '4' => [ProviderException::INTERNAL, 'Terminate failed.'],
            '5' => [ProviderException::INTERNAL, 'Refund failed.'],
            '6' => [ProviderException::END_USER_NO_CREDIT, 'Prepaid failed.'],
            '7' => [ProviderException::INTERNAL, 'Transaction failed.'],
            '8' => [ProviderException::INTERNAL, 'Subscription terminate failed.'],
            '10' => [ProviderException::END_USER_ALREADY_SUBSCRIBED, 'Subscriber already has an active subscription.'],
            '11' => [ProviderException::INTERNAL, 'Fraud prevention.'],
            '12' => [ProviderException::INTERNAL, 'Captcha wrong.'],
            '13' => [ProviderException::SETTINGS, 'Invalid parameter.'],
            '101' => [ProviderException::SETTINGS, 'Invalid parameter.'],
            '109' => [ProviderException::ACTION_REFUSED, 'Transaction in wrong status.'],
            '110' => [ProviderException::INVALID_PIN, 'Wrong PIN.'],
            '111' => [ProviderException::TOO_MANY_ATTEMPTS, 'Too many PIN attempts - transaction closed.'],
            '112' => [ProviderException::END_USER_CANCELLATION, 'Subscriber aborted transaction.'],
            '113' => [ProviderException::END_USER_INVALID_MSISDN, 'No route to operator or subscriberid invalid.'],
            '121' => [ProviderException::END_USER_INVALID_MSISDN, 'Subscriberid unascertainable.'],
            '126' => [ProviderException::SENDING_PIN_FAILED, 'Sending TAN SMS failed.'],
            '150' => [ProviderException::SUBSCRIPTION_UNKNOWN, 'Subscription id unknown.'],
            '151' => [ProviderException::SETTINGS, 'Subscription id not unique.'],
            '152' => [ProviderException::END_USER_NOT_ELIGIBLE, 'subscription terminated.'],
            '200' => [ProviderException::INTERNAL, 'Internal server error.'],
            '201' => [ProviderException::INTERNAL, 'System currently unavailable.'],
            '1001' => [ProviderException::INTERNAL, 'Transaction failed after TAN transmission.'],
        ]
    ];

    public function getSubscriptionRegistrationContext(TransactionRequest $request): ?SubscriptionRegistrationContextInterface
    {
        return $this->createSubscriptionRegistrationContext()
            ->withProviderSubscriptionInactive()
            ->withoutChargingOnSubscribe()
            ->withoutSubscriptionAbortOnFailedCharge()
            ->withoutChargeMessage();
    }

    protected function declareNotificationSessionHandler(SessionHandler $handler): void
    {
        $handler
            ->forType(TransactionInterface::TYPE_SUBSCRIPTION)
                ->activeSessionFromCache()
                    ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
                ->endSession()
                ->activeSessionFromDB()
                    ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
                ->endSession();
        $handler->forType(TransactionInterface::TYPE_ONESHOT)
            ->activeSessionFromCache()
                ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
            ->endSession()
            ->activeSessionFromDB()
                ->withInput(TransactionContextDefinitionInterface::INPUT_SESSION)
            ->endSession();
        $handler->forType(TransactionInterface::TYPE_CANCELLATION)
            ->passiveSessionFromCache()
                ->withInput(TransactionContextDefinitionInterface::INPUT_SUBSCRIPTION_ID)
            ->endSession()
            ->passiveSessionFromDB()
                ->withInput(TransactionContextDefinitionInterface::INPUT_SUBSCRIPTION_ID)
            ->endSession();
        $handler->forType(TransactionInterface::TYPE_REFUND)
            ->passiveSessionFromCache()
                ->withInput(TransactionContextDefinitionInterface::INPUT_INVOICE_ID_EXTERNAL)
            ->endSession()
            ->passiveSessionFromDB()
                ->withInput(TransactionContextDefinitionInterface::INPUT_INVOICE_ID_EXTERNAL)
            ->endSession();
        $handler->forType(TransactionInterface::TYPE_INVOICE)
            ->activeSessionFromCache()
                ->withInput(TransactionContextDefinitionInterface::INPUT_RELATED_ID)
                ->withStatus(TransactionInterface::STATUS_PENDING)
            ->endSession();
    }

    public function setSdk(API $sdk): self
    {
        $this->sdk = $sdk;
        return $this;
    }

    public function getSdk(): API
    {
        return $this->sdk;
    }

    public function getContentClassFromThematic(int $thematic): int
    {
        switch ($thematic) {
            case Thematic::DATING:
                return ContentClass::CHAT_FLIRT;
            case Thematic::KIDS:
            case Thematic::MOBILE_CONTENT:
            case Thematic::GAMING:
                return ContentClass::GAME;
            case Thematic::BOOK:
            case Thematic::LEARNING:
            case Thematic::LIFESTYLE:
            case Thematic::SWEEPSTAKE:
            case Thematic::DV_STUDIO:
            case Thematic::OTHERS:
            case Thematic::SPORT:
                return ContentClass::NEWS_INFO;
            case Thematic::VIDEO:
            case Thematic::VOD:
                return ContentClass::VIDEOCLIP;
            case Thematic::MUSIC:
                return ContentClass::MUSIC;
            case Thematic::SEXY_ADULT:
                return ContentClass::EROTIC;
        }
        return ContentClass::NEWS_INFO;
    }

    public static function translateOperatorIdToMccmnc(string $operatorId): int
    {
        switch ($operatorId) {
            case '262-01':
                return 26201;
            case '262-02':
                return 26202;
            case '262-07':
                return 26207;
            case '262-MODE':
                return 26213;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported operatorId %s', $operatorId));
        }
    }
}
