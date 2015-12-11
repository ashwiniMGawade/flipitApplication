<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151210121232 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $shopExcelInformation = $schema->getTable('shopExcelInformation');
        $shopExcelInformation->addColumn('filename', 'string', array('notnull' => 0));
    }

    public function down(Schema $schema)
    {
        $shopExcelInformation = $schema->getTable('shopExcelInformation');
        $shopExcelInformation->dropColumn('filename');
    }
}
