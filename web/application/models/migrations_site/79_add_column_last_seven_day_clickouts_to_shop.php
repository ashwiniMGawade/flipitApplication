<?php
class AddColumnLastSevenDayClickoutsToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'lastSevendayClickouts', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('shop', 'lastSevendayClickouts');
    }
}
