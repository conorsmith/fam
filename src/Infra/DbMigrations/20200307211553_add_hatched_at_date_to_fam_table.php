<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddHatchedAtDateToFamTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `fams`
                ADD COLUMN `hatched_at` DATETIME
        ");

        $this->execute("
            UPDATE fams SET hatched_at = NOW()
        ");

        $this->execute("
            ALTER TABLE `fams`
                MODIFY COLUMN `hatched_at` DATETIME NOT NULL
        ");
    }
}
