<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

use ConorSmith\Fam\Infra\FamRepositoryDb;
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

        $this->db->update("fams", [
            'last_feed_time' => $this->now->format("Y-m-d H:i:s"),
            'feed_ttl'       => FEED_TTL,
        ], [
            'id' => $id,
        ]);

        header("Location: /{$id}");
    }
}
