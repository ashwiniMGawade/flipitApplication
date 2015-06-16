<?php
class AddChainIdShop extends Doctrine_Migration_Base
{

    public function up()
    {
        $this->addColumn( 'shop', 'chainId', 'integer', 20 ,
                        array('default' => '', 'notnull' => false ));
    }

    public function down()
    {
         $this->removeColumn('shop', 'chainId');
    }

}
