<?php

class AddTablePageWidgets extends Doctrine_Migration_Base
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
            'widgetId' => array(
                'type'   => 'integer',
                'length' => 20
            ),
            'widget_type' => array(
                'type'   => 'string',
                'length' => 255
            ),
            'position' => array(
                'type'   => 'integer',
                'length' => 20

            ),
            'deleted' => array(
                'type'   => 'boolean',
                'length' => 1
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
        $this->createTable('page_widgets', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('page_widgets');
    }
}
