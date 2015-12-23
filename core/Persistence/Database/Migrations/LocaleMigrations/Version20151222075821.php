<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151222075821 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->createTable('newsletterCampaignOffers');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('campaignId', 'integer');
        $table->addColumn('offerId', 'bigint');
        $table->addColumn('position', 'bigint', array('comment'=> 'holds the position of the offer among campaign offers'));
        $table->addColumn('section', 'boolean', array('default' => 1, 'length' => 1, 'comment' => '1-section one ,2-section 2'));
        $table->addColumn('deleted', 'boolean', array('default' => 0, 'length' => 1, 'comment' => '1-deleted ,0-not deleted'));
        $table->addColumn('createdAt', 'datetime', array('length' => 12));
        $table->addColumn('updatedAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
        $table->addForeignKeyConstraint('newsletterCampaigns', array('campaignId'), array('id'), array("onDelete" => "CASCADE"), 'newsletterCampaignOffers_campaignId_newsletterCampaigns_id');
        $table->addForeignKeyConstraint('offer', array('offerId'), array('id'), array("onDelete" => "CASCADE"), 'newsletterCampaignOffers_offerId_offer_id');

    }

    public function down(Schema $schema)
    {
        $schema->dropTable('newsletterCampaignOffers');

    }
}
