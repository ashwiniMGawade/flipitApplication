<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration: To add the default header and footer settings for the newsletter campaigns!"
 */
class Version20151201123456 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `settings`
          (`id`, `name`, `value`, `status`, `created_at`, `updated_at`, `deleted`, `label`, `isEditable`)
          VALUES (NULL, 'NEWSLETTER_CAMPAIGN_HEADER', '', '1', NOW(), NOW(), '0', NULL, '0'),
          (NULL, 'NEWSLETTER_CAMPAIGN_FOOTER', '', '1', NOW(), NOW(), '0', NULL, '0')
          ");

    }

    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `settings` WHERE `settings`.`name` = 'NEWSLETTER_CAMPAIGN_HEADER' or `settings`.`name` = 'NEWSLETTER_CAMPAIGN_FOOTER'");
    }
}
