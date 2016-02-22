<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160107150231 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->connection->executeQuery("alter table newsletterCampaigns  CHANGE  `scheduledTime`  `scheduledTime` DATETIME NULL DEFAULT NULL");
    }


    public function down(Schema $schema)
    {
        $this->connection->executeQuery("alter table newsletterCampaigns  CHANGE  `scheduledTime`  `scheduledTime` BIGINT NULL DEFAULT NULL");
    }
}
