<?php
class AddEmailcampainTable extends Doctrine_Migration_Base
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
            'sender' => array(
                    'type'   => 'string'
            ),
            'subject' => array(
                    'type'   => 'string'
            ),
            'header' => array(
                    'type'   => 'string'
            ),
            'footer' => array(
                    'type'   => 'string'
            ),
            'status' => array(
                    'type'   => 'integer',
                    'length' => '2'
            ),
            'recipients' => array(
                'type'   => 'boolean',
                'length' => 1
            ),
            'send_at' => array(
                'type'   => 'datetime'
            ),
            'created_at' => array(
                'type'   => 'timestamp',
                'length' => 12
            ),
            'updated_at' => array(
                'type'   => 'timestamp',
                'length' => 12
            ),
            'deleted' => array(
                'type'   => 'boolean',
                'length' => 1
            )
        );

        $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8'
        );

        $this->createTable( 'emailcampain', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'emailcampain' );
    }
}
