<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160201131017 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `landingPages` ADD `brandingCss` TEXT NULL DEFAULT NULL AFTER `content`");
    }

    public function down(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `landingPages` DROP `brandingCss`");
    }
}
