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
        if ($popularCouponCode->hasForeignKey('offerid')) {
            $popularCouponCode->removeForeignKey('offerid');
        }

        if ($popularCouponCode->hasIndex('offerid')) {
            $popularCouponCode->dropIndex('offerid');
        }

        $popularCouponCode->addForeignKeyConstraint('offer', array('offerid'), array('id'), array('onDelete' => 'CASCADE'), 'offerid');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
