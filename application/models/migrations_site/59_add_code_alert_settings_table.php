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
