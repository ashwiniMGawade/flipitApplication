<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151202065354 extends AbstractMigration
{
    /*
    * @param Schema $schema
    */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('newsletterCampaigns');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('campaignName', 'string', array('notnull' => 0));
        $table->addColumn('campaignSubject', 'string', array('notnull' => 0));
        $table->addColumn('senderName', 'string', array('notnull' => 0));
        $table->addColumn('senderEmail', 'string', array('notnull' => 0));
        $table->addColumn('header', 'text', array('notnull' => 0));
        $table->addColumn('headerBanner', 'string', array('notnull' => 0));
        $table->addColumn('headerBannerURL', 'string', array('notnull' => 0));
        $table->addColumn('footer', 'text', array('notnull' => 0));
        $table->addColumn('footerBanner', 'string', array('notnull' => 0));
        $table->addColumn('footerBannerURL', 'string', array('notnull' => 0));
        $table->addColumn('offerPartOneTitle', 'string', array('notnull' => 0));
        $table->addColumn('offerPartTwoTitle', 'string', array('notnull' => 0));
        $table->addColumn('scheduledStatus', 'boolean', array('default' => 0, 'length' => 1, 'comment' => '1-Scheduled, 0-Not Scheduled'));
        $table->addColumn('scheduledTime', 'datetime', array('notnull' => 0, 'length' => 12));
        $table->addColumn('newsletterSentTime', 'datetime', array('notnull' => 0, 'length' => 12));
        $table->addColumn('receipientCount', 'integer',  array('notnull' => 0));
        $table->addColumn('deleted', 'boolean', array('default' => 0, 'length' => 1, 'comment' => '1-deleted, 0-Not deleted'));
        $table->addColumn('createdAt', 'datetime', array('notnull' => 1, 'length' => 12));
        $table->addColumn('updatedAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('newsletterCampaigns');
    }
}
