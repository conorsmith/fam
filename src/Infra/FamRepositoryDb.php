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
        $row = $this->db->fetchAssoc("SELECT * FROM fams WHERE id = ?", [$id]);

        $feedDeadline = DateTime::createFromFormat("Y-m-d H:i:s", $row['last_feed_time']);
        $feedDeadline->add(new DateInterval("PT{$row['feed_ttl']}S"));
        $feedDeadline = DateTimeImmutable::createFromMutable($feedDeadline);

        return new class($row['name'], $row['species_id'], $feedDeadline)
        {
            /** @var string */
            private $name;

            /** @var string */
            private $speciesId;

            /** @var DateTimeImmutable */
            private $feedDeadline;

            public function __construct(string $name, string $speciesId, DateTimeImmutable $feedDeadline)
            {
                $this->name = $name;
                $this->speciesId = $speciesId;
                $this->feedDeadline = $feedDeadline;
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
                return $now < $this->feedDeadline;
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

                $secondsToDeadline = $this->feedDeadline->getTimestamp() - $now->getTimestamp();
                return FEED_TTL - $secondsToDeadline < 10;
            }
        };
    }
}
