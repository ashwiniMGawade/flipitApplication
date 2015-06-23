<?php
class AddHowtoSubSubTitleColumnShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'howtoSubSubTitle', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('shop', 'howtoSubSubTitle');
    }
}
