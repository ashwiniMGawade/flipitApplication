<?php
class AddColumnCodeAlertSendDateVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'code_alert_send_date', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('visitor', 'code_alert_send_date');
    }
}
