<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151006091227 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $settingsTable = $schema->getTable('settings');
        $settingsTable->addColumn('label', 'string', array('length' => 255, 'notnull' => 0, 'default' => null));
        $settingsTable->addColumn('isEditable', 'boolean', array( 'notnull' => 1, 'default' => 0));
    }

    public function postUp(Schema $schema) {
        $this->connection->executeQuery("INSERT INTO `settings` (`id`, `name`, `value`, `status`, `created_at`, `updated_at`, `deleted`, `label`, `isEditable`) VALUES (NULL, 'HTML_LANG', NULL, '1', NOW(), NOW(), '0', 'HTML Lang', '1')");
    }

    public function down(Schema $schema)
    {
        $settingsTable = $schema->getTable('settings');
        $settingsTable->dropColumn('label');
        $settingsTable->dropColumn('isEditable');
        $this->addSql("DELETE FROM `settings` WHERE `settings`.`name` = 'HTML_LANG'");
    }
}
