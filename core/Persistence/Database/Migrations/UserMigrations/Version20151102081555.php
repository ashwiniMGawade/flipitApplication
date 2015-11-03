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
        $this->connection->executeQuery("INSERT INTO `splashPage` (`id`, `content`, `image`, `popularShops`, `updatedBy`, `updatedAt`) VALUES (NULL, '<h2>Flipit.com — your online couponing portal</h2>
<p>Flipit is your tool of choice for saving money on your online shopping. We work closely with leading brands worldwide to bring you the best deals and discount codes available on the web.</p>
<p>Now you’re here, you’re just a few clicks away from knocking a percentage or amount off your online order! It won’t cost you a thing and you don’t even need to register with us to take advantage of our great discounts. We check, verify and publish brand new voucher codes each and every day, so you can find a way to save money on your online purchase all year round.</p>
<p>Simply select your country, search for your favourite brand, then click on your favourite deal to reveal your desired coupon code. When you’re ready, proceed to checkout and copy the voucher code from Flipit.com into the designated box, and enjoy your discount! Saving online really is that easy</p>
<p>We hope you enjoy saving money with us!</p>',
        'splash.jpg',
        '<div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>Singapore</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/sg/zalora\">Zalora</a></li>
                <li><a href=\"http://flipit.com/sg/lazada-singapore\">Lazada</a></li>
                <li><a href=\"http://flipit.com/sg/redmart\">RedMart</a></li>
                <li><a href=\"http://flipit.com/sg/foodpanda\">Foodpanda</a></li>
                <li><a href=\"http://flipit.com/sg/hotels-com\">Hotels.com</a></li>
            </ul>
            <h3>Sweden</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/se/adidas\">Adidas</a></li>
                <li><a href=\"http://flipit.com/se/miinto\">Miinto</a></li>
                <li><a href=\"http://flipit.com/se/zoovillage\">Zoovillage</a></li>
            </ul>
        </div>
        <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>France</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/fr/aliexpress\">Aliexpress</a></li>
                <li><a href=\"http://flipit.com/fr/kiabi\">Kiabi</a></li>
                <li><a href=\"http://flipit.com/fr/ms-mode\">MS mode</a></li>
                <li><a href=\"http://flipit.com/fr/hema\">HEMA</a></li>
            </ul>
            <h3>Indonesie</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/id/lazada\">Lazada</a></li>
                <li><a href=\"http://flipit.com/id/bilna\">Bilna</a></li>
                <li><a href=\"http://flipit.com/id/blibli\">Blibli</a></li>
                <li><a href=\"http://flipit.com/id/zalora\">Zalora</a></li>
            </ul>
        </div>
        <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>Spain</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/es/kiabi\">Kiabi.es</a></li>
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
        </div>
        <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>Denmark</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/dk/stylepit\">Stylepit</a></li>
                <li><a href=\"http://flipit.com/dk/asos\">ASOS</a></li>
                <li><a href=\"http://flipit.com/dk/hotels-com\">Hotels.com</a></li>
                <li><a href=\"http://flipit.com/dk/pixizoo\">PixiZoo</a></li>
                <li><a href=\"http://flipit.com/dk/bona-parte\">BON\'A PARTE</a></li>
            </ul>
            <h3>Malaysia</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/my/groupon\">Groupon</a></li>
                <li><a href=\"http://flipit.com/my/zalora\">Zalora</a></li>
                <li><a href=\"http://flipit.com/my/foodpanda\">Foodpanda</a></li>
                <li><a href=\"http://flipit.com/my/lazada\">Lazada</a></li>
            </ul>
        </div>
        <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>Belgie</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/be/zalando\">Zalando</a></li>
                <li><a href=\"http://flipit.com/be/bol-com\">bol.com</a></li>
                <li><a href=\"http://flipit.com/be/collishop\">ColliShop</a></li>
                <li><a href=\"http://flipit.com/be/albelli\">Albelli</a></li>
                <li><a href=\"http://flipit.com/be/smartphoto\">Smartphoto</a></li>
                <li><a href=\"http://flipit.com/be/hema\">HEMA</a></li>
                <li><a href=\"http://flipit.com/be/sarenza\">Sarenza</a></li>
                <li><a href=\"http://flipit.com/be/lensonline\">LensOnline</a></li>
                <li><a href=\"http://flipit.com/be/center-parcs\">Center Parcs</a></li>
                <li><a href=\"http://flipit.com/be/hellofresh\">HelloFresh</a></li>
            </ul>
        </div>
        <div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
            <h3>New Zealand</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/nz/value-basket\">Value Basket</a></li>
                <li><a href=\"http://flipit.com/nz/mighty-ape\">Mighty Ape</a></li>
            </ul>
            <h3>Portugal</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/pt/edreams\">eDreams</a></li>
                <li><a href=\"http://flipit.com/pt/fnac\">Fnac</a></li>
                <li><a href=\"http://flipit.com/pt/worten\">Worten</a></li>
            </ul>
            <h3>Australia</h3>
            <ul class=\"list-unstyled\">
                <li><a href=\"http://flipit.com/au/groupon\">Groupon</a></li>
                <li><a href=\"http://flipit.com/au/crazy-sales\">CrazySales</a></li>
                <li><a href=\"http://flipit.com/au/get-glasses\">Get Glasses</a></li>
            </ul>
        </div>',
'1', NOW())");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('splashPage');
    }
}
