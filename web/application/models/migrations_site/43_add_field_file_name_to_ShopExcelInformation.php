<?php

class AddFieldFileNameToShopExcelInformation extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shopExcelInformation', 'filename', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('shopExcelInformation', 'filename');
    }
}
