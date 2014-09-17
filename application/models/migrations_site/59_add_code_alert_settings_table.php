<?php
class AddCodeAlertSettingsTable extends Doctrine_Migration_Base
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
            'email_subject' => array(
                'type'   => 'string',
                'length' => 255
            ),
            'email_header' => array(
                'type'   => 'blob'
            ),
            'code_alert_schedule' => array(
                'type' => 'boolean',
                'notnull' => false ,
                'default' => 0,
                'length' => 1,
                'comment' => '1-scheduled ,0-manual'
            ),
            'code_alert_status' => array(
                'type' => 'boolean',
                'notnull' => false ,
                'default' => 0,
                'length' => 1,
                'comment' => '1-sent ,0-unsent'
            ),
            'code_alert_schedule_time' => array(
                'type'   => 'timestamp',
                'length' => 12
            ),
            'deleted' => array(
                'type'   => 'boolean',
                'length' => 1,
                'default' => 0
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
        $this->createTable('code_alert_settings', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('code_alert_settings');
    }
}
