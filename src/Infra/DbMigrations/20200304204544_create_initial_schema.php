<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInitialSchema extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `fams` (
              `id` varchar(36) NOT NULL,
              `name` varchar(256) NOT NULL,
              `species_id` varchar(256) NOT NULL,
              `last_feed_time` datetime DEFAULT NULL,
              `feed_ttl` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            )
        ");
    }
}
