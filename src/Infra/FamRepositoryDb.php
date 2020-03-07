<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra;

use ConorSmith\Fam\Domain\Fam;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class FamRepositoryDb
{
    /** @var Connection */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function find(string $id): Fam
    {
        $famRow = $this->db->fetchAssoc("SELECT * FROM fams WHERE id = ?", [$id]);
        $feedRows = $this->db->fetchAll("SELECT * FROM fam_feeds WHERE fam_id = ? ORDER BY feed_time", [$id]);

        $feedTimes = [];

        foreach ($feedRows as $feedRow) {
            $feedTimes[] = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $feedRow['feed_time']);
        }

        return new Fam(Uuid::fromString($famRow['id']), $famRow['name'], $famRow['species_id'], $feedTimes);
    }

    public function save(Fam $fam): void
    {
        $this->db->transactional(function (Connection $db) use ($fam) {

            $db->executeQuery(
                "DELETE FROM fam_feeds WHERE fam_id = ?",
                [
                    $fam->getId(),
                ]
            );

            /** @var DateTimeImmutable $feedTime */
            foreach ($fam->getFeedTimes() as $feedTime) {
                $db->insert("fam_feeds", [
                    'id'        => Uuid::uuid4(),
                    'fam_id'    => $fam->getId(),
                    'feed_time' => $feedTime->format("Y-m-d H:i:s"),
                ]);
            }

        });
    }
}
