<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150910142342 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->createTable('landingPages');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('shopId', 'bigint');
        $table->addColumn('title', 'string');
        $table->addColumn('permalink', 'string');
        $table->addColumn('subTitle', 'string', array('notnull' => 0));
        $table->addColumn('metaTitle', 'string', array('notnull' => 0));
        $table->addColumn('metaDescription', 'text', array('notnull' => 0));
        $table->addColumn('content', 'blob', array('notnull' => 0));
        $table->addColumn('status', 'boolean', array('default' => 1, 'length' => 1, 'comment' => '1-available ,0-used'));
        $table->addColumn('offlineSince', 'datetime', array('length' => 12));
        $table->addColumn('createdAt', 'datetime', array('length' => 12));
        $table->addColumn('updatedAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
        $table->addForeignKeyConstraint('shop', array('shopId'), array('id'), array("onDelete" => "CASCADE"), 'landingPages_shopId_shop_id');
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('landingPages');
    }
}
