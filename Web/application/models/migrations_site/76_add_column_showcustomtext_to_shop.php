<?php
class AddShowColumnCustomTextShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'shop',
            'showcustomtext',
            'boolean',
            null,
            array(
                'default' => 0,
                'notnull' => true
            )
        );
    }

    public function down()
    {
        $this->removeColumn('shop', 'showcustomtext');
    }
}
