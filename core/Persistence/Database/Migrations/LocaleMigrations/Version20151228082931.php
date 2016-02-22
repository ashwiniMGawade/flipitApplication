<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151228082931 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `newsletterCampaigns` CHANGE `scheduledStatus` `scheduledStatus` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0-Not Scheduled, 1-Scheduled, 2 - Triggered, 3 - Sent'");
    }

    public function down(Schema $schema)
    {
    }
}
