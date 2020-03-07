<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

use DateTimeImmutable;
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
            'hatched_at'     => $this->now->format("Y-m-d H:i:s"),
        ]);

        for ($i = 0; $i < 4; $i++) {
            $this->db->insert("fam_feeds", [
                'id'        => Uuid::uuid4(),
                'fam_id'    => $id,
                'feed_time' => $this->now->format("Y-m-d H:i:s"),
            ]);
        }

        header("Location: /{$id}");
    }
}
