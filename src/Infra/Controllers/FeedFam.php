<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

use ConorSmith\Fam\Infra\FamRepositoryDb;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class FeedFam
{
    /** @var Connection */
    private $db;

    /** @var DateTimeImmutable */
    private $now;

    public function __construct(Connection $db, DateTimeImmutable $now)
    {
        $this->db = $db;
        $this->now = $now;
    }

    public function handle(): void
    {
        $id = substr($_SERVER['REQUEST_URI'], 1, 36);

        $fam = (new FamRepositoryDb($this->db))->find($id);

        if (!$fam->isAlive($this->now)) {
            http_response_code(500);
            return;
        }

        $this->db->transactional(function ($db) use ($id) {

            $startOfFeedingWindow = DateTime::createFromFormat("U", strval($this->now->getTimestamp()));
            $startOfFeedingWindow->sub(new DateInterval("PT" . FEED_TTL . "S"));
            $startOfFeedingWindow = DateTimeImmutable::createFromMutable($startOfFeedingWindow);

            $this->db->update("fams", [
                'last_feed_time' => $this->now->format("Y-m-d H:i:s"),
                'feed_ttl'       => FEED_TTL,
            ], [
                'id' => $id,
            ]);

            $this->db->executeQuery(
                "DELETE FROM fam_feeds WHERE fam_id = ? AND feed_time < ?",
                [
                    $id,
                    $startOfFeedingWindow->format("Y-m-d H:i:s"),
                ]
            );

            $this->db->insert("fam_feeds", [
                'fam_id'    => $id,
                'feed_time' => $this->now->format("Y-m-d H:i:s"),
            ]);

        });

        header("Location: /{$id}");
    }
}
