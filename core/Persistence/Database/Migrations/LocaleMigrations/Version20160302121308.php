<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160302121308 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `settings` (`id`, `name`, `value`, `status`, `created_at`, `updated_at`, `deleted`, `label`, `isEditable`) VALUES (NULL, 'SHOW_SIGNUP_WIDGET_ON_GLP', '1', '1', NOW(), NOW(), '0', 'Signup Widget On GLP', '1')");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `settings` WHERE `settings`.`name` = 'SHOW_SIGNUP_WIDGET_ON_GLP'");
    }
}
