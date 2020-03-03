<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Infra\Controllers;

final class ShowStartGamePage
{
    public function handle(): void
    {
        include __DIR__ . "/../Templates/Start.php";
    }
}
