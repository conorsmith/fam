<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra;

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

        return new class($famRow['name'], $famRow['species_id'], $feedTimes)
        {
            /** @var string */
            private $name;

            /** @var string */
            private $speciesId;

            /** @var array */
            private $feedTimes;

            public function __construct(
                string $name,
                string $speciesId,
                array $feedTimes
            ) {
                $this->name = $name;
                $this->speciesId = $speciesId;
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
                return $this->getCalories($now) > 0
                    && $this->getCalories($now) < 40 * 500;
            }

            public function getDistress(DateTimeImmutable $now): float
            {
                if (!$this->isAlive($now)) {
                    return 0;
                }

                if ($this->getCalories($now) >= 3 * 500) {
                    return 0;
                }

                return ((3 * 500) - $this->getCalories($now)) / (3 * 500);
            }

            public function isHappy(DateTimeImmutable $now): bool
            {
                if (!$this->isAlive($now)) {
                    return false;
                }

                if ($this->isSick($now)) {
                    return false;
                }

                $secondsSinceLastFeed = $now->getTimestamp() - $this->getLatestFeedTime()->getTimestamp();

                return $secondsSinceLastFeed < 10;
            }

            public function isSick(DateTimeImmutable $now): bool
            {
                if (!$this->isAlive($now)) {
                    return false;
                }

                return $this->getCalories($now) > 6 * 500;
            }

            private function getCalories(DateTimeImmutable $now): float
            {
                $calories = 0;

                /** @var DateTimeImmutable $feedTime */
                foreach ($this->feedTimes as $feedTime) {
                    $secondsSinceLastFeed = $now->getTimestamp() - $feedTime->getTimestamp();
                    $secondsRemainingForFeed = FEED_TTL - $secondsSinceLastFeed;
                    if ($secondsRemainingForFeed > 0) {
                        $calories += 500 * $secondsRemainingForFeed / FEED_TTL;
                    }
                }

                return $calories;
            }

            private function getLatestFeedTime(): DateTimeImmutable
            {
                return $this->feedTimes[count($this->feedTimes) - 1];
            }
        };
    }
}
