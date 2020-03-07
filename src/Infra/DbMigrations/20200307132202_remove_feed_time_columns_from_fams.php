<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveFeedTimeColumnsFromFams extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `fams`
                DROP COLUMN `last_feed_time`,
                DROP COLUMN `feed_ttl`
        ");
    }
}
