<?php
class AddColumnCodeAlertSendDateFavoriteShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('favorite_shop', 'code_alert_send_date', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('favorite_shop', 'code_alert_send_date');
    }
}
