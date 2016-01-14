<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151229063217 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("alter table newsletterCampaigns  CHANGE  `scheduledTime`  `scheduledTime` BIGINT NULL DEFAULT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->connection->executeQuery("alter table newsletterCampaigns CHANGE  `scheduledTime`  `scheduledTime` DATETIME NULL DEFAULT NULL");

    }
}
