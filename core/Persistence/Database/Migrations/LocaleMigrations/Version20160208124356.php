<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160208124356 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE  `shop` ADD  `sidebarPosition` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '0 - Right, 1 - Left' AFTER  `offerCount`");
    }

    public function down(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `shop` DROP `sidebarPosition`");
    }
}
