<?php

declare(strict_types=1);

namespace Platform\Bundle\MBE\MBE4\Jobs;

use Asset\DataManipulation\CarbonDateTime;
use Asset\Database\Parser\Filter;
use Asset\Database\Parser\QueryBuilder;
use AtomSDK\Exception\AtomException;
use Platform\Bundle\MBE\MBE4\Wrapper;
use Platform\Core\Exception\InternalException;
use Platform\Core\Model\OneShot;
use Platform\Core\Model\Operation;
use Platform\Core\Repository\References\BillingMode;
use Platform\Core\Repository\References\Offer;
use Platform\Core\Synchronizer\PlatformSynchronizer;
use Throwable;

use function count;
use function implode;
use function sprintf;

/**
 * @package Platform\Bundle\MBE\MBE4\Jobs
 * @property Wrapper $bundle
 */
class OneShotReminder extends PlatformSynchronizer
{
    protected function synchronize(array $params = []): void
    {
        $countryCode = $this->getRequest()->getQueryParamsMap()->get('countryCode', 'DE');
        $this->sendOneShotReminder($this->bundle->getPlatformId(), $countryCode);
    }

    /**
     * @return array<int, string>
     */
    private function getAtomOffers(int $platformId, string $countryCode): array
    {
        $statement = $this->atomSDK->prepare('offers')
            ->setFilter('equal.platform.id', (string) $platformId)
            ->setFilter('equal.country.id', $countryCode)
            ->setFilter('equal.billingmode.id', (string) BillingMode::ONE_SHOT);

        try {
            $response = $statement->fetchAll();
        } catch (AtomException $e) {
            throw new InternalException(InternalException::SETTINGS);
        }

        $result = [];
        /** @var Offer $offer */
        foreach ($response->offers() as $offer) {
            $result[] = $offer->getId();
        }
        return $result;
    }

    private function sendOneShotReminder(int $platformId, string $countryCode): void
    {
        $this->container->get('query_runner')->useConnectionPreset('synchro');
        $offerIds = $this->getAtomOffers($platformId, $countryCode);

        if (count($offerIds) < 1) {
            $this->logger->info('No offers found');
            return;
        }

        $this->logger->info(sprintf('Offers to be processed: %s', implode(',', $offerIds)));

        $upper = CarbonDateTime::tomorrow()->endOfDay();
        $lower = $upper->clone()->startOfDay();

        $query = QueryBuilder::oneshot()
            ->include(
                OneShot::operation()::path(),
                Filter::equal(OneShot::operation()::fieldCountryId(), $countryCode),
                Filter::equal(OneShot::operation()::fieldPlatformId(), $platformId)
            )
            ->filter(Filter::equal(OneShot::fieldCountryId(), $countryCode))
            ->filter(Filter::in(OneShot::operation()::fieldOfferId(), $offerIds))
            ->filter(Filter::equal(OneShot::operation()::fieldType(), Operation::TYPE_ONE_SHOT))
            ->filter(Filter::equal(OneShot::operation()::fieldStatus(), Operation::STATUS_OK))
            ->filter(Filter::equal(OneShot::operation()::fieldPlatformId(), $platformId))
            ->filter(Filter::notNull((OneShot::fieldExpirationDate())))
            ->filter(Filter::between(OneShot::fieldExpirationDate(), $lower, $upper));

        $resultSet = $query->setVerbose(false)->useIteratorYield(true)->fetch();

        $linesTotal = 0;
        foreach ($resultSet as $result) {
            $linesTotal++;
            /** @var OneShot $oneshot */
            $oneshot = $result['main']->mapObject();
            $this->logger->info(sprintf('Sending message to oneshot purchases id: %s', $oneshot->getId()));
            try {
                $this->sendOneShotMessage($oneshot->getId(), 'reminder_notice', ['1day_before'], $oneshot->getCountryId());
            } catch (Throwable $error) {
                $this->logger->notice(sprintf('Error trying to send message: %s', $error->getMessage()));
            }
        }

        if ($linesTotal === 0) {
            $this->logger->info('There are no oneshots in order to send reminders');
            return;
        }

        $this->logger->info(sprintf('%d oneshots found for %s in %s', $linesTotal, $this->bundle->getBillingProviderNiceName(), $countryCode));
    }
}
