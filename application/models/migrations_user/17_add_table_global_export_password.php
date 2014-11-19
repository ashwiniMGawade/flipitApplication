<?php

class AddGlobalExportPasswordTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
            'id' => array(
                'type'     => 'integer',
                'length'   => 2,
                'primary'  => 1,
                'autoincrement' => 1,
                'notnull'  => 1
            ),
            'password' => array(
                'type'   => 'string',
                'length'   => 255
            ),
            'exportType' => array(
                'type'   => 'string',
                'length'   => 50,
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

        $this->createTable('global_export_password', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('global_export_password');
    }
}
