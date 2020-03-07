<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFamPlaysTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `fam_plays` (
                `id` varchar(36) NOT NULL,
                `fam_id` varchar(36) NOT NULL,
                `play_time` datetime NOT NULL,
                PRIMARY KEY (`id`)
            )
        ");
    }
}
