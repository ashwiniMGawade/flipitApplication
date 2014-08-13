<?php
class AddRobotTable extends Doctrine_Migration_Base
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
                'website' => array(
                        'type'   => 'string'
                ),
                'content' => array(
                        'type'   => 'blob',
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
        $this->createTable('robot', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('robot');
    }
}
