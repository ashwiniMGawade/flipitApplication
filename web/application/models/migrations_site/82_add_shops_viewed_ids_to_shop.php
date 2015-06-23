<?php
class AddShopsViewedIdsToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'shopsViewedIds', 'string', 100);
    }

    public function down()
    {
        $this->removeColumn('shop', 'shopsViewedIds');
    }
}
