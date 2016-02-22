<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151217132625 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $popularCouponCode = $schema->getTable('popular_code');
        if ($popularCouponCode->hasForeignKey('popular_code_offerid_offer_id')) {
            $popularCouponCode->removeForeignKey('popular_code_offerid_offer_id');
        }

        if ($popularCouponCode->hasIndex('offerid')) {
            $popularCouponCode->dropIndex('offerid');
        }

        $popularCouponCode->addForeignKeyConstraint('offer', array('offerid'), array('id'), array('onDelete' => 'CASCADE'), 'popular_code_offerid_offer_id');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
