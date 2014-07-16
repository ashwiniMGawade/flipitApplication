<?php
class AddViewcountToOfferShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'totalviewcount', 'integer', 20 );
        $this->addColumn( 'offer', 'totalviewcount', 'integer', 20 );
    }

    public function down()
    {
        $this->removeColumn('shop', 'totalviewcount');
        $this->removeColumn('offer', 'totalviewcount');
    }
}
