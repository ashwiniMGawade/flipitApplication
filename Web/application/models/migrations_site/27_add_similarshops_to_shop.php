<?php
class AddSimillarShopsToShop extends Doctrine_Migration_Base
{
    public function up()
    {

        $this->addColumn( 'shop', 'showSimliarShops', 'boolean', null ,
                        array(	'default' => 0 ,
                                'comment' => 'if true then displays same shops as well as shop related to same category',
                                'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn( 'shop', 'showSimliarShops');
    }
}
