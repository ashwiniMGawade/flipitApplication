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
                `content`='<header class=\"row\">
                            <h2>Why you should use Flipit?</h2>
                            </header>
                            <div class=\"row cols-holder\">
                            <div class=\"col-xs-12\">
                            <article class=\"col\"><i class=\"icon icon-star\"></i>
                            <h3><a href=\"#\">Only on Flipit!</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            <article class=\"col\"><i class=\"icon icon-alert\"></i>
                            <h3><a href=\"#\">Coupon Alert</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            <article class=\"col\"><i class=\"icon icon-veriefied\"></i>
                            <h3><a href=\"#\">All Codes Veriefied</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            <article class=\"col\"><i class=\"icon icon-safe\"></i>
                            <h3><a href=\"#\">Safe to use</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            <article class=\"col\"><i class=\"icon icon-account\"></i>
                            <h3><a href=\"#\">No Account Required</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            <article class=\"col\"><i class=\"icon icon-free\"></i>
                            <h3><a href=\"#\">100% Free</a></h3>
                            <p>A little story about coupon codes and discounts and of course promo codes.</p>
                            </article>
                            </div>
                            </div>',
                `footer`='<p><strong class=\"title\">Lorum ipsum popie jopie zinnetje voor Editor</strong></p>
                    <ul class=\"list-inline logos-holder\">
                        <li><a href=\"#\"><img alt=\"flipit\" src=\"/public/images/flipit-logo-blue.png\" /></a></li>
                        <li><a href=\"#\"><img alt=\"Q shope keurmerk\" src=\"/public/images/logo-qshops.png\" /></a></li>
                        <li><a href=\"#\"><img alt=\"logo\" src=\"/public/images/imbull-logo.png\" /></a></li>
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
                    <p>Copyright &copy; 2009-2015&nbsp;<a href=\"#\">Imbull.com</a></p>
                    </div>
',
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
