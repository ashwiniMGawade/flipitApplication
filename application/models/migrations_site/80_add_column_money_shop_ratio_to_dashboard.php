<?php
class AddColumnMoneyShopRatioDashboard extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'dashboard',
            'money_shop_ratio',
            'integer',
            null,
            array(
                'default' => 0,
                'notnull' => true
            )
        );
    }

    public function down()
    {
        $this->removeColumn('dashboard', 'money_shop_ratio');
    }
}
