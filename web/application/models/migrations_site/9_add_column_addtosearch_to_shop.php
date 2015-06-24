<?php
class AddColumnShowChainshop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'showchains', 'boolean', null ,
                        array('default' => 0 ,
                              'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn('shop', 'showchains');
    }
}
