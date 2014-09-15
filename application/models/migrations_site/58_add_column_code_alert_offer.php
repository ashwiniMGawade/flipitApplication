<?php
class AddColumnCodeAlertOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('offer', 'code_alert', 'string');
    }

    public function down()
    {
        $this->removeColumn('offer', 'code_alert');
    }
}
