<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151006100906 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('URLSettings');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('url', 'string', array('notnull' => 1));
        $table->addColumn('status', 'boolean', array('default' => 1, 'length' => 1, 'comment' => '1-On, 0-Off'));
        $table->addColumn('createdAt', 'datetime', array('notnull' => 1, 'length' => 12));
        $table->addColumn('updatedAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
        $table->addIndex(array('url'), 'url_idx');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('URLSettings');
    }
}
