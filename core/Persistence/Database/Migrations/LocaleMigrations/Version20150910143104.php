<?php
namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150910143104 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DELETE FROM ref_offer_page WHERE ref_offer_page.pageid NOT IN (SELECT page.id FROM page)');
        $refOfferPage = $schema->getTable('ref_offer_page');
        if ($refOfferPage->hasForeignKey('ref_offer_page_pageid_page_id')) {
            $refOfferPage->removeForeignKey('ref_offer_page_pageid_page_id');
        }
        $refOfferPage->addForeignKeyConstraint('page', array('pageid'), array('id'), array('onDelete' => 'CASCADE'), 'ref_offer_page_pageid_page_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $refOfferPage = $schema->getTable('ref_offer_page');
        if ($refOfferPage->hasForeignKey('ref_offer_page_pageid_page_id')) {
            $refOfferPage->removeForeignKey('ref_offer_page_pageid_page_id');
        }
    }
}
