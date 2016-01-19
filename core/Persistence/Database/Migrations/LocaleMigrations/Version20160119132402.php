<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160119132402 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $localSettingsTable = $schema->getTable('locale_settings');
        $localSettingsTable->addColumn('expiredCouponLogo', 'string', array('notnull' => 0));

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $localSettingsTable = $schema->getTable('locale_settings');
        $localSettingsTable->dropColumn('expiredCouponLogo');

    }
}
