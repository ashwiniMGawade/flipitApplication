<?php
class AddColumnHowToGuideSlugToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'howtoguideslug', 'string', 100);
    }

    public function down()
    {
        $this->removeColumn('shop', 'howtoguideslug');
    }
}
