<?php

class AddfeaturedtextToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'featuredtext', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('shop', 'featuredtext');
    }
}
