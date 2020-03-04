<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFamFeedsTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `fam_feeds` (
              `fam_id` varchar(36) NOT NULL,
              `feed_time` datetime NOT NULL,
              PRIMARY KEY (`fam_id`, `feed_time`)
            )
        ");
    }
}
