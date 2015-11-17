<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151103113930 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('splashImages');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('image', 'string', array('notnull' => 1));
        $table->addColumn('position', 'integer', array('notnull' => 1));
        $table->addColumn('createdAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('splashImages');
    }
}
