<?php
class AddEmailsTable extends Doctrine_Migration_Base
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
                'type' => array(
                        'type'   => 'string'
                ),
                'header' => array(
                        'type'   => 'string'

                ),
                'body' => array(
                        'type'   => 'string'

                ),
                'footer' => array(
                        'type'   => 'string'

                ),
                'schedule' => array(
                        'type'   => 'string'

                ),
                'test' => array(
                        'type'   => 'string'

                ),
                'status' => array(
                        'type'   => 'integer',
                        'length' => 1

                ),
                'send_date' => array(
                        'type'   => 'date'

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

        $this->createTable( 'emails', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'emails' );
    }
}
