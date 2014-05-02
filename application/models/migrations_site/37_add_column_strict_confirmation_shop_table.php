<?php
class AddColumnStrcitConfirmationShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'strictconfirmation', 'boolean', null ,
                        array('default' => 0 ,
                              'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn('shop', 'strictconfirmation');
    }
}
