<?php
declare(strict_types=1);

use ConorSmith\Fam\Infra\Controllers\FeedFam;
use ConorSmith\Fam\Infra\Controllers\ShowFamPage;
use ConorSmith\Fam\Infra\Controllers\ShowStartGamePage;
use ConorSmith\Fam\Infra\Controllers\StartGame;
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

define('FEED_TTL', 60 * 60 * 24);

$now = new DateTimeImmutable("now", new DateTimeZone("Europe/Dublin"));

if ($_SERVER['REQUEST_METHOD'] === "GET"
    && $_SERVER['REQUEST_URI'] === "/"
) {
    (new ShowStartGamePage)->handle();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST"
    && $_SERVER['REQUEST_URI'] === "/"
) {
    (new StartGame($db, $now))->handle();
    exit;
}

if (preg_match("#^/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$#", $_SERVER['REQUEST_URI']) === 1) {
    (new ShowFamPage($db, $now))->handle();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST"
    && substr($_SERVER['REQUEST_URI'], 37) === "/feed"
) {
    (new FeedFam($db, $now))->handle();
    exit;
}

http_response_code(404);
