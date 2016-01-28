<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160128070933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `page_widgets` ADD `referenceId` BIGINT NULL DEFAULT NULL AFTER `widget_type`");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->connection->executeQuery("ALTER TABLE `page_widgets` DROP `referenceId`");
    }
}
