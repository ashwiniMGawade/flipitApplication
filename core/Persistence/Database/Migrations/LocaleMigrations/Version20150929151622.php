<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150929151622 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $aboutTable = $schema->getTable('about');
        $aboutTable->addColumn('permalink', 'string', array('length' => 255, 'notnull' => 0, 'default' => null));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $aboutTable = $schema->getTable('about');
        $aboutTable->dropColumn('permalink');
    }
}
