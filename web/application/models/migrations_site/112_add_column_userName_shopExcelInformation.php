<?php

class AddColumnUserNameShopExcelInformation extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shopExcelInformation', 'userName', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('shopExcelInformation', 'userName');
    }
}
