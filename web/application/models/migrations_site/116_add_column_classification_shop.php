<?php
class AddClassificationColumnShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'classification', 'integer', '1', array('default' => 1, 'comment' => '1=A, 2=A+, 3=AA, 4=AA+, 5=AAA'));
    }

    public function down()
    {
        $this->removeColumn('shop', 'classification');
    }
}
