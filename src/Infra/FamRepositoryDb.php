<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class FamRepositoryDb
{
    /** @var Connection */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function find(string $id)
    {
        $famRow = $this->db->fetchAssoc("SELECT * FROM fams WHERE id = ?", [$id]);
        $feedRows = $this->db->fetchAll("SELECT * FROM fam_feeds WHERE fam_id = ?", [$id]);

        $feedTimes = [];

        foreach ($feedRows as $feedRow) {
            $feedTimes[] = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $feedRow['feed_time']);
        }

        $feedDeadline = DateTime::createFromFormat("Y-m-d H:i:s", $famRow['last_feed_time']);
        $feedDeadline->add(new DateInterval("PT{$famRow['feed_ttl']}S"));
        $feedDeadline = DateTimeImmutable::createFromMutable($feedDeadline);

        return new class($famRow['name'], $famRow['species_id'], $feedDeadline, $feedTimes)
        {
            /** @var string */
            private $name;

            /** @var string */
            private $speciesId;

            /** @var DateTimeImmutable */
            private $feedDeadline;

            /** @var array */
            private $feedTimes;

            public function __construct(
                string $name,
                string $speciesId,
                DateTimeImmutable $feedDeadline,
                array $feedTimes
            ) {
                $this->name = $name;
                $this->speciesId = $speciesId;
                $this->feedDeadline = $feedDeadline;
                $this->feedTimes = $feedTimes;
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getSpeciesId(): string
            {
                return $this->speciesId;
            }

            public function isAlive(DateTimeImmutable $now): bool
            {
                return $now < $this->feedDeadline
                    && $this->countFeedTimesInWindow($now) < 40;
            }

            public function getDistress(DateTimeImmutable $now): float
            {
                if (!$this->isAlive($now)) {
                    return 0;
                }

                $secondsToDeadline = $this->feedDeadline->getTimestamp() - $now->getTimestamp();

                $distressPeriod = FEED_TTL / 3 * 2;

                if ($secondsToDeadline > $distressPeriod) {
                    return 0;
                }

                $timeDistressed = $distressPeriod - $secondsToDeadline;

                return $timeDistressed / $distressPeriod;
            }

            public function isHappy(DateTimeImmutable $now): bool
            {
                if (!$this->isAlive($now)) {
                    return false;
                }

                if ($this->isSick($now)) {
                    return false;
                }

                $secondsToDeadline = $this->feedDeadline->getTimestamp() - $now->getTimestamp();
                return FEED_TTL - $secondsToDeadline < 10;
            }

            public function isSick(DateTimeImmutable $now): bool
            {
                if (!$this->isAlive($now)) {
                    return false;
                }

                return $this->countFeedTimesInWindow($now) > 6;
            }

            private function countFeedTimesInWindow(DateTimeImmutable $now): int
            {
                $startOfFeedingWindow = DateTime::createFromFormat("U", strval($now->getTimestamp()));
                $startOfFeedingWindow->sub(new DateInterval("PT" . FEED_TTL . "S"));
                $startOfFeedingWindow = DateTimeImmutable::createFromMutable($startOfFeedingWindow);

                $feedTimesInWindow = [];

                foreach ($this->feedTimes as $feedTime) {
                    if ($feedTime > $startOfFeedingWindow) {
                        $feedTimesInWindow[] = $feedTime;
                    }
                }

                return count($feedTimesInWindow);
            }
        };
    }
}
