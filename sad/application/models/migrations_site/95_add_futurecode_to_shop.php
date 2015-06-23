<?php

class AddFuturecodeToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'shop',
            'futurecode',
            'boolean',
            null
        );
    }

    public function down()
    {
        $this->removeColumn('shop', 'futurecode');
    }
}
