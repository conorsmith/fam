<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddExplicitIdToFamFeedsTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `fam_feeds`
                ADD COLUMN `id` varchar(36) NOT NULL FIRST
        ");

        $this->execute("
            UPDATE fam_feeds SET id = (SELECT UUID())
        ");

        $this->execute("
            ALTER TABLE `fam_feeds`
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (`id`)
        ");
    }
}
