<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160121144404 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `settings`
          (`id`, `name`, `value`, `status`, `created_at`, `updated_at`, `deleted`, `label`, `isEditable`)
          VALUES (NULL, 'expiredCouponLogo', '', '1', NOW(), NOW(), '0', NULL, '0')
          ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `settings` WHERE `settings`.`name` = 'expiredCouponLogo'");

    }
}
