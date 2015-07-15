<?php
class RemoveColumnUserIdFromshopExcelInformation extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('shopExcelInformation', 'userId');
    }

    public function down()
    {
        $this->removeColumn('shopExcelInformation', 'userId');
    }
}
