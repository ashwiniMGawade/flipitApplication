<?php

class AddfeaturedtextdateToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'featuredtextdate', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('shop', 'featuredtextdate');
    }
}
