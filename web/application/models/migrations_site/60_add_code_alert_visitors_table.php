<?php
class AddCodeAlertVisitorsTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
            'id' => array(
                'type'     => 'integer',
                'length'   => 20,
                'primary'  => 1,
                'autoincrement' => 1,
                'notnull'  => 1
            ),
            'offerId' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'visitorId' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'created_at' => array(
                'type'   => 'timestamp',
                'length' => 12
            ),
            'updated_at' => array(
                'type'   => 'timestamp',
                'length' => 12
            )
        );
        $options = array(
            'type'    => 'INNODB',
            'charset' => 'utf8'
        );
        $this->createTable('code_alert_visitors', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('code_alert_visitors');
    }
}
