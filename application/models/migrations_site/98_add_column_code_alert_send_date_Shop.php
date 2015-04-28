<?php
class AddColumnCodeAlertSendDateShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shop', 'code_alert_send_date', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('shop', 'code_alert_send_date');
    }
}
