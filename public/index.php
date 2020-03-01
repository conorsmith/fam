<?php
declare(strict_types=1);

use ConorSmith\Fam\Infra\FamRepositoryDb;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . "/../vendor/autoload.php";

if (getenv('DB_NAME') === false) {
    Dotenv::createImmutable(__DIR__ . "/../")->load();
}

$db = DriverManager::getConnection([
    'dbname'   => getenv('DB_NAME'),
    'user'     => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'host'     => getenv('DB_HOST'),
    'driver'   => "pdo_mysql",
]);

define('FEED_TTL', 60 * 60 * 12);

$now = new DateTimeImmutable("now", new DateTimeZone("Europe/Dublin"));

if ($_SERVER['REQUEST_METHOD'] === "GET"
    && $_SERVER['REQUEST_URI'] === "/"
) {
    include __DIR__ . "/../src/Infra/Templates/Start.php";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST"
    && $_SERVER['REQUEST_URI'] === "/"
) {
    $id = Uuid::uuid4();

    if (!array_key_exists('name', $_POST)
        || !array_key_exists('egg', $_POST)
        || $_POST['name'] === ""
        || $_POST['egg'] === ""
    ) {
        http_response_code(500);
        exit;
    }

    $db->insert("fams", [
        'id'             => $id,
        'name'           => $_POST['name'],
        'species_id'     => $_POST['egg'],
        'last_feed_time' => $now->format("Y-m-d H:i:s"),
        'feed_ttl'       => FEED_TTL,
    ]);

    header("Location: /{$id}");
    exit;
}

if (preg_match("#^/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$#", $_SERVER['REQUEST_URI']) === 1) {

    $id = substr($_SERVER['REQUEST_URI'], 1, 37);

    $fam = (new FamRepositoryDb($db))->find($id);

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
        'isAlive'      => $fam->isAlive($now),
        'isDistressed' => $fam->isDistressed($now),
        'isHappy'      => $fam->isHappy($now),
    ];

    include __DIR__ . "/../src/Infra/Templates/Home.php";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST"
    && substr($_SERVER['REQUEST_URI'], 37) === "/feed"
) {
    $id = substr($_SERVER['REQUEST_URI'], 1, 36);

    $fam = (new FamRepositoryDb($db))->find($id);

    if (!$fam->isAlive($now)) {
        http_response_code(500);
        exit;
    }

    $db->update("fams", [
        'last_feed_time' => $now->format("Y-m-d H:i:s"),
        'feed_ttl'       => FEED_TTL,
    ], [
        'id' => $id,
    ]);

    header("Location: /{$id}");
    exit;
}

http_response_code(404);
