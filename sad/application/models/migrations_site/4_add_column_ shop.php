<?php
class AddColumnShop extends Doctrine_Migration_Base
{

    public function up()
    {
        $this->addColumn( 'shop', 'showsignupoption', 'boolean', null ,
                        array('default' => 0 ,
                              'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn( 'shop', 'showsignupoption');
    }

}
