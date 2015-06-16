<?php
class AddStatusChainItem extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'chain_item', 'status', 'boolean', 1,array('default' => 1) );
        $this->addColumn( 'chain_item', 'shopId', 'integer', 20 ,array('default' => null) );

    }

    public function down()
    {
        $this->removeColumn('chain_item', 'status');
        $this->removeColumn('chain_item', 'shopId');

    }
}
