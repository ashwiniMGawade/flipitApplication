<?php
class AddColumnShopAndOfferClickoutsShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'shopAndOfferClickouts', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('shop', 'shopAndOfferClickouts');
    }
}
