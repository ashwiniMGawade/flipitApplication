<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151210143950 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $splashPage = $schema->getTable('splashPage');
        $splashPage->dropColumn('visitorsPerMonthCount');
        $splashPage->dropColumn('verifiedActionCount');
        $splashPage->dropColumn('newsletterSignupCount');
        $splashPage->dropColumn('retailerOnlineCount');
        $splashPage->addColumn('statistics', 'blob', array('notnull' => 1));
    }

    public function postUp(Schema $schema)
    {

        $this->connection->executeQuery("UPDATE `splashPage`
              SET
                `statistics`='<div class=\"row cols-wrp\">
                                <div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">
                                    <div class=\"col\">
                                        <strong class=\"count\">1.500.000+</strong>
                                        <p>bezoekers per maand</p>
                                    </div>
                                </div>
                                <div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">
                                    <div class=\"col num\">
                                        <strong class=\"count\">45.000+</strong>
                                        <p>Geverifieerde acties</p>
                                    </div>
                                </div>
                                <div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">
                                    <div class=\"col news-letter\">
                                        <strong class=\"count\">650.000+</strong>
                                        <p>Nieuwsbrief aanmeldingen</p>
                                    </div>
                                </div>
                                <div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">
                                    <div class=\"col retail\">
                                        <strong class=\"count\">10.000+</strong>
                                        <p>Retailers online</p>
                                    </div>
                                </div>
                            </div>'
              WHERE `id`=1"
        );
    }

    public function down(Schema $schema)
    {
        $splashPage = $schema->getTable('splashPage');
        $splashPage->dropColumn('statistics');
        $splashPage->addColumn('visitorsPerMonthCount', 'integer', array('length' => 11));
        $splashPage->addColumn('verifiedActionCount', 'integer', array('length' => 11));
        $splashPage->addColumn('newsletterSignupCount', 'integer', array('length' => 11));
        $splashPage->addColumn('retailerOnlineCount', 'integer', array('length' => 11));
    }
}
