<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160121140006 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `landingPages` ADD `refUrl` TEXT NULL DEFAULT NULL AFTER `permalink`");
    }

    public function down(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `landingPages` DROP `refUrl`");
    }
}
