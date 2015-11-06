<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151102081555 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('splashPage');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('content', 'blob', array('notnull' => 1));
        $table->addColumn('image', 'string', array('notnull' => 1));
        $table->addColumn('popularShops', 'blob', array('notnull' => 1));
        $table->addColumn('updatedBy', 'integer', array('notnull' => 1, 'length' => 5));
        $table->addColumn('updatedAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
    }

    public function postUp(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `splashPage` (`id`, `content`, `image`, `popularShops`, `updatedBy`, `updatedAt`)
            VALUES (
                NULL,
                '<h2>Flipit.com — Your Online Couponing Portal</h2>
                    <p>Flipit is your tool of choice for saving money on your online shopping. We work closely with leading brands worldwide to bring you the best deals and discount codes available on the web.</p>
                    <p>Now you’re here, you’re just a few clicks away from knocking a percentage or amount off your online order! It won’t cost you a thing and you don’t even need to register with us to take advantage of our great discounts. We check, verify and publish brand new voucher codes each and every day, so you can find a way to save money on your online purchase all year round.</p>
                    <p>Simply select your country, search for your favourite brand, then click on your favourite deal to reveal your desired coupon code. When you’re ready, proceed to checkout and copy the voucher code from Flipit.com into the designated box, and enjoy your discount! Saving online really is that easy.</p>
                    <p>We hope you enjoy saving money with us!</p>',
                'splash.jpg',
                '<div class=\"row\">
                    <div class=\"col-xs-12\"><h2>Promo Codes and Discounts for 6500+ Online Stores</h2></div>
                </div>
                <div class=\"row\">
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/be/\">Belgium</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/be/zalando\">Zalando</a></li>
                            <li><a href=\"http://flipit.com/be/bol-com\">bol.com</a></li>
                            <li><a href=\"http://flipit.com/be/collishop\">ColliShop</a></li>
                            <li><a href=\"http://flipit.com/be/albelli\">Albelli</a></li>
                            <li><a href=\"http://flipit.com/be/smartphoto\">Smartphoto</a></li>
                            <li><a href=\"http://flipit.com/be/hema\">HEMA BE</a></li>
                            <li><a href=\"http://flipit.com/be/sarenza\">Sarenza</a></li>
                            <li><a href=\"http://flipit.com/be/lensonline\">LensOnline</a></li>
                            <li><a href=\"http://flipit.com/be/center-parcs\">Center Parcs</a></li>
                            <li><a href=\"http://flipit.com/be/hellofresh\">HelloFresh</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/se/\">Sweden</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/se/adidas\">Adidas</a></li>
                            <li><a href=\"http://flipit.com/se/miinto\">Miinto</a></li>
                            <li><a href=\"http://flipit.com/se/zoovillage\">Zoovillage</a></li>
                        </ul>
                    </div>
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/es/\">Spain</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/es/kiabi\">Kiabi ES</a></li>
                            <li><a href=\"http://flipit.com/es/pc-componentes\">PcComponentes</a></li>
                            <li><a href=\"http://flipit.com/es/sportium\">Sportium</a></li>
                            <li><a href=\"http://flipit.com/es/casa-del-libro\">Casa del Libro</a></li>
                            <li><a href=\"http://flipit.com/es/vueling\">Vueling</a></li>
                            <li><a href=\"http://flipit.com/es/groupalia\">Groupalia</a></li>
                            <li><a href=\"http://flipit.com/es/rakuten-es\">Rakuten</a></li>
                            <li><a href=\"http://flipit.com/es/letsbonus\">LetsBonus</a></li>
                            <li><a href=\"http://flipit.com/es/descuentos-perfumes\">PerfumesClub</a></li>
                            <li><a href=\"http://flipit.com/es/promociones-farma\">Promofarma</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/au/\">Australia</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/au/groupon\">Groupon AU</a></li>
                            <li><a href=\"http://flipit.com/au/crazy-sales\">CrazySales</a></li>
                            <li><a href=\"http://flipit.com/au/get-glasses\">Get Glasses AU</a></li>
                        </ul>
                    </div>
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/it/\">Italy</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/it/home24\">Home24</a></li>
                            <li><a href=\"http://flipit.com/it/desigual\">Desigual</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/br/\">Brazil</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/br/adidas\">adidas</a></li>
                            <li><a href=\"http://flipit.com/br/sephora\">Sephora</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/pl/\">Poland</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/pl/interhome\">Interhome</a></li>
                            <li><a href=\"http://flipit.com/pl/answear\">Answear</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/pt/\">Portugal</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/pt/edreams\">eDreams</a></li>
                            <li><a href=\"http://flipit.com/pt/fnac\">FNAC</a></li>
                            <li><a href=\"http://flipit.com/pt/worten\">Worten</a></li>
                        </ul>
                    </div>
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/sg/\">Singapore</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/sg/zalora\">Zalora SG</a></li>
                            <li><a href=\"http://flipit.com/sg/lazada-singapore\">Lazada SG</a></li>
                            <li><a href=\"http://flipit.com/sg/redmart\">RedMart</a></li>
                            <li><a href=\"http://flipit.com/sg/foodpanda\">Foodpanda</a></li>
                            <li><a href=\"http://flipit.com/sg/hotels-com\">Hotels.com SG</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/id/\">Indonesie</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/id/lazada\">Lazada ID</a></li>
                            <li><a href=\"http://flipit.com/id/bilna\">Bilna</a></li>
                            <li><a href=\"http://flipit.com/id/blibli\">Blibli</a></li>
                            <li><a href=\"http://flipit.com/id/zalora\">Zalora ID</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/nz/\">New Zealand</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/nz/value-basket\">Value Basket</a></li>
                            <li><a href=\"http://flipit.com/nz/mighty-ape\">Mighty Ape</a></li>
                        </ul>
                    </div>
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/fr/\">France</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/fr/aliexpress\">Aliexpress FR</a></li>
                            <li><a href=\"http://flipit.com/fr/kiabi\">Kiabi FR</a></li>
                            <li><a href=\"http://flipit.com/fr/ms-mode\">MS mode</a></li>
                            <li><a href=\"http://flipit.com/fr/hema\">HEMA FR</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/ch/\">Switzerland</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/ch/deindeal\">DeinDeal</a></li>
                            <li><a href=\"http://flipit.com/ch/reifendirekt\">Reifendirekt</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/no/\">Norway</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/no/nelly\">Nelly</a></li>
                            <li><a href=\"http://flipit.com/no/jollyroom\">Jollyroom</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/us/\">United States</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/us/monnier-freres\">Monnier Freres</a></li>
                            <li><a href=\"http://flipit.com/us/iventure\">iVenture</a></li>
                        </ul>
                    </div>
                    <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
                        <h3><a href=\"http://flipit.com/de/\">Germany</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/de/otto\">Otto</a></li>
                            <li><a href=\"http://flipit.com/de/redcoon\">Redcoon</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/dk/\">Denmark</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/dk/stylepit\">Stylepit</a></li>
                            <li><a href=\"http://flipit.com/dk/asos\">ASOS</a></li>
                            <li><a href=\"http://flipit.com/dk/hotels-com\">Hotels.com DK</a></li>
                            <li><a href=\"http://flipit.com/dk/pixizoo\">PixiZoo</a></li>
                            <li><a href=\"http://flipit.com/dk/bona-parte\">BON\'A PARTE</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/in/\">India</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/in/amazon-in\">Amazon</a></li>
                            <li><a href=\"http://flipit.com/in/flipkart\">Flipkart</a></li>
                        </ul>
                        <h3><a href=\"http://flipit.com/my/\">Malaysia</a></h3>
                        <ul class=\"list-unstyled\">
                            <li><a href=\"http://flipit.com/my/zalora\">Zalora MY</a></li>
                            <li><a href=\"http://flipit.com/my/lazada\">Lazada MY</a></li>
                        </ul>
                    </div>
                </div>',
                '1',
                NOW()
                )"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('splashPage');
    }
}
