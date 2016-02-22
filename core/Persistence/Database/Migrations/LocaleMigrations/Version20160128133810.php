<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160128133810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `URLSettings` ADD `hotjarStatus` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `status`");
    }

    public function down(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `URLSettings` DROP `hotjarStatus`");
    }
}
