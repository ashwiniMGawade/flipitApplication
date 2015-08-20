<?php
class AddCoulmnStartDateEndDateWidget extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('widget', 'startDate', 'datetime');
        $this->addColumn('widget', 'endDate', 'datetime');
    }

    public function down()
    {
        $this->removeColumn('widget', 'startDate');
        $this->removeColumn('widget', 'endDate');
    }
}
