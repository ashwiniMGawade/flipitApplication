<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151028103022 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `settings` (`id`, `name`, `value`, `status`, `created_at`, `updated_at`, `deleted`, `label`, `isEditable`) VALUES (NULL, 'SHOW_FLOATING_COUPON', '0', '1', NOW(), NOW(), '0', 'Display Floating Coupon', '1')");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `settings` WHERE `settings`.`name` = 'SHOW_FLOATING_COUPON'");
    }
}
