<?php
class AddColumnLocaleToChain extends Doctrine_Migration_Base
{
    public function up()
    {
    	$this->addColumn( 'chain_item', 'locale', 'string', 255, array(
    		'type' => 'string',
    		'length' => '255',
    		));
    }
    
    public function down()
    {
        $this->removeColumn('chain_item','locale');
    }
}