<?php
class AddDashboardTable extends Doctrine_Migration_Base
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
                'message' => array(
                        'type'   => 'string'
                ),
                'no_of_offers' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'no_of_shops' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'no_of_clickouts' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'no_of_subscribers' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'total_no_of_offers' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'total_no_of_shops' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'total_no_of_shops_online_code' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'total_no_of_shops_online_code_lastweek' => array(
                        'type'   => 'integer',
                        'length' => 11

                ),
                'total_no_members' => array(
                        'type'   => 'integer',
                        'length' => 11

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

        $this->createTable( 'dashboard', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'dashboard' );
    }
}
