<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151204092642 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $splashPage = $schema->getTable('splashPage');
        $splashPage->addColumn('infoImage', 'string', array('notnull' => 1));
        $splashPage->addColumn('footer', 'blob', array('notnull' => 1));
        $splashPage->addColumn('visitorsPerMonthCount', 'integer', array('length' => 11));
        $splashPage->addColumn('verifiedActionCount', 'integer', array('length' => 11));
        $splashPage->addColumn('newsletterSignupCount', 'integer', array('length' => 11));
        $splashPage->addColumn('retailerOnlineCount', 'integer', array('length' => 11));
    }

    public function postUp(Schema $schema)
    {

        $this->connection->executeQuery("UPDATE `splashPage`
              SET
                `infoImage`='splash-info.jpg',
                `footer`='<strong class=\"title\">Lorum ipsum popie jopie zinnetje voor Arthur</strong>
                            <ul class=\"list-inline logos-holder\">
                                <li><a href=\"#\"><img src=\"images/flipit-logo.png\" alt=\"flipit\"></a></li>
                                <li><a href=\"#\"><img src=\"images/logo-qshops.png\" alt=\"Q shope keurmerk\"></a></li>
                                <li><a href=\"#\"><img src=\"images/icon-logo.png\" alt=\"logo\"></a></li>
                            </ul>
                            <div class=\"center-box\">
                                <nav class=\"add-nav\">
                                    <ul>
                                        <li><a href=\"#\">Flipit.com is a subsidiary of Imbull</a></li>
                                        <li>Chamber of Commerce #34339618</li>
                                    </ul>
                                </nav>
                                <p>van Ostadestraat 149, 1073TK Amsterdam - Netherlands</p>
                            </div>
                            <div class=\"center-box\">
                                <nav class=\"add-nav\">
                                    <ul>
                                        <li><a href=\"#\">Contact</a></li>
                                       <li><a href=\"#\">Privacy Policy</a></li>
                                        <li><a href=\"#\">Disclaimer</a></li>
                                    </ul>
                                </nav>
                                <p>Copyright &copy; 2009-2015 <a href=\"#\">Imbull.com</a></p>
                            </div>',
               `visitorsPerMonthCount`='1500000',
               `verifiedActionCount`='45000',
               `newsletterSignupCount`='650000',
               `retailerOnlineCount`='10000'
              WHERE `id`=1"
        );
    }

    public function down(Schema $schema)
    {
        $splashPage = $schema->getTable('splashPage');
        $splashPage->dropColumn('infoImage');
        $splashPage->dropColumn('footer');
        $splashPage->dropColumn('visitorsPerMonthCount');
        $splashPage->dropColumn('verifiedActionCount');
        $splashPage->dropColumn('newsletterSignupCount');
        $splashPage->dropColumn('retailerOnlineCount');
    }
}
