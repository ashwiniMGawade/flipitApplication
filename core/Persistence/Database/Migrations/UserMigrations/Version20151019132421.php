<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151019132421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $codeAlertQueueTable = $schema->getTable('splash');
        $codeAlertQueueTable->addColumn('shopId', 'integer', array('length' => 11));
        $codeAlertQueueTable->addColumn('position', 'integer', array('length' => 11));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $codeAlertQueueTable = $schema->getTable('splash');
        $codeAlertQueueTable->dropColumn('shopId');
        $codeAlertQueueTable->dropColumn('position');
    }
}
