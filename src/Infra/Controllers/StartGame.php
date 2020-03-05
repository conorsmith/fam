<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

use DateTime;
use DateTimeImmutable;
use DateInterval;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

final class StartGame
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
        $id = Uuid::uuid4();

        if (!array_key_exists('name', $_POST)
            || !array_key_exists('egg', $_POST)
            || $_POST['name'] === ""
            || $_POST['egg'] === ""
        ) {
            http_response_code(500);
            return;
        }

        $this->db->insert("fams", [
            'id'             => $id,
            'name'           => $_POST['name'],
            'species_id'     => $_POST['egg'],
            'last_feed_time' => $this->now->format("Y-m-d H:i:s"),
            'feed_ttl'       => FEED_TTL,
        ]);

        $firstHatchFeed = $this->createTimeNSecondsAgo(1);
        $secondHatchFeed = $this->createTimeNSecondsAgo(2);
        $thirdHatchFeed = $this->createTimeNSecondsAgo(3);
        $fourthHatchFeed = $this->createTimeNSecondsAgo(4);

        $this->db->insert("fam_feeds", [
            'fam_id'    => $id,
            'feed_time' => $firstHatchFeed->format("Y-m-d H:i:s"),
        ]);

        $this->db->insert("fam_feeds", [
            'fam_id'    => $id,
            'feed_time' => $secondHatchFeed->format("Y-m-d H:i:s"),
        ]);

        $this->db->insert("fam_feeds", [
            'fam_id'    => $id,
            'feed_time' => $thirdHatchFeed->format("Y-m-d H:i:s"),
        ]);

        $this->db->insert("fam_feeds", [
            'fam_id'    => $id,
            'feed_time' => $fourthHatchFeed->format("Y-m-d H:i:s"),
        ]);

        header("Location: /{$id}");
    }

    private function createTimeNSecondsAgo(int $n): DateTimeImmutable
    {
        $datetime = DateTime::createFromFormat("U", strval($this->now->getTimestamp()));
        $datetime->sub(new DateInterval("PT{$n}S"));
        return DateTimeImmutable::createFromMutable($datetime);
    }
}
