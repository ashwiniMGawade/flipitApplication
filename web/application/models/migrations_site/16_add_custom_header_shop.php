<?php
class AddCustomHeaderShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'customheader', 'string', 1024 ,
                array('notnull' => false ));
    }

    public function down()
    {
        $this->removeColumn('shop', 'customheader');
    }
}
