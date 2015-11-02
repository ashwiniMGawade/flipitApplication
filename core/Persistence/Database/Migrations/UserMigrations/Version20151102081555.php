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
<p>We hope you enjoy saving money with us!</p>', 'splash.jpg', '<div class=\"col-xs-6 col-sm-4 col-md-2 col-lg-2\">
        <h3>Singapore</h3>
        <ul class=\"list-unstyled\">
            <li><a href=\"http://dev.flipit.com/sg/zalora\">Zalora</a></li>
            <li><a href=\"http://dev.flipit.com/sg/lazada-singapore\">Lazada</a></li>
            <li><a href=\"http://dev.flipit.com/sg/redmart\">RedMart</a></li>
            <li><a href=\"http://dev.flipit.com/sg/foodpanda\">Foodpanda</a></li>
            <li><a href=\"http://dev.flipit.com/sg/hotels-com\">Hotels.com</a></li>
        </ul>
</div>', '1', NOW())");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('splashPage');
    }
}
