<?php
class AddTableWidgetLocation extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
            'id' => array(
                'type'     => 'integer',
                'length'   => 10,
                'primary'  => 1,
                'autoincrement' => 1,
                'notnull'  => 1
            ),
            'position' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'pagetype' => array(
                'type'   => 'string',
                'length' => 100
            ),
            'location' => array(
                'type'   => 'string',
                'length' => 100
            ),
            'relatedid' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'widgettype' => array(
                'type'   => 'string',
                'length' => 100
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
        $this->createTable('widget_location', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('widget_location');
    }
}
