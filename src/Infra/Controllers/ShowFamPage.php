<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

use ConorSmith\Fam\Infra\FamRepositoryDb;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class ShowFamPage
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
        $id = substr($_SERVER['REQUEST_URI'], 1, 37);

        $fam = (new FamRepositoryDb($this->db))->find($id);

        $speciesIconsById = [
            1  => "dog",
            2  => "cat",
            3  => "otter",
            4  => "spider",
            5  => "kiwi-bird",
            6  => "horse",
            7  => "dragon",
            8  => "frog",
            9  => "fish",
            10 => "dove",
            11 => "crow",
            12 => "hippo",
        ];

        $fam = (object) [
            'id'           => $id,
            'name'         => $fam->getName(),
            'speciesIcon'  => $speciesIconsById[$fam->getSpeciesId()],
            'isAlive'      => $fam->isAlive($this->now),
            'distress'     => $fam->getDistress($this->now),
            'isHappy'      => $fam->isHappy($this->now),
            'isSick'       => $fam->isSick($this->now),
        ];

        include __DIR__ . "/../Templates/Home.php";
    }
}
