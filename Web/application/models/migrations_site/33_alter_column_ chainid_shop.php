<?php
class AlterColumnChainIdShop extends Doctrine_Migration_Base
{


    public function up()
    {
        $this->renameColumn("shop", "chainId", "chainItemId");
    }

    public function down()
    {
        $this->renameColumn("shop", "chainItemId" , "chainId") ;
    }


}
