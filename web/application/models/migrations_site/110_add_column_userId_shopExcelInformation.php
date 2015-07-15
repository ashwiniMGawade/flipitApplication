<?php
class AddColumnUserIdShopExcelInformation extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shopExcelInformation', 'userId', 'integer', 10);
    }

    public function down()
    {
        $this->removeColumn('shopExcelInformation', 'userId');
    }
}
