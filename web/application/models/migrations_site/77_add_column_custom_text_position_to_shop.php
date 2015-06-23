<?php
class AddCustomTextPositionShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'customtextposition', 'integer', 10);
    }

    public function down()
    {
        $this->removeColumn('shop', 'customtextposition');
    }
}