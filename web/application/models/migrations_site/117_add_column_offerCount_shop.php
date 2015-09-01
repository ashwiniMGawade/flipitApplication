<?php
class AddOfferCountColumnShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'offerCount', 'integer', '11', array('default' => 0));
    }

    public function down()
    {
        $this->removeColumn('shop', 'offerCount');
    }
}
