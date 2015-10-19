<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151019090421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $codeAlertQueueTable = $schema->getTable('code_alert_queue');
        $codeAlertQueueTable->addColumn('started', 'boolean', array('notnull' => 0, 'default' => 0, 'length' => 1, 'comment' => '1-Started sending email ,0-Mail sending is not started yet'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $codeAlertQueueTable = $schema->getTable('code_alert_queue');
        $codeAlertQueueTable->dropColumn('started');
    }
}
