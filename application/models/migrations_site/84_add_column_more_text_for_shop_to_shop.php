<?php
class AddColumnMoreTextForShopToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'shop',
            'moretextforshop',
            'blob',
            null
        );
    }

    public function down()
    {
        $this->removeColumn('shop', 'moretextforshop');
    }
}
