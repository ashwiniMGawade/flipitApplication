<?php
class AddCouponCodeTable extends Doctrine_Migration_Base
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
                'offerid' => array(
                        'type'   => 'integer',
                        'length' => 20

                ),
                'code' => array(
                        'type'   => 'string',
                        'length' => 10

                ),
                'status' => array(
                        'type'   => 'boolean',
                        'default' => 1,
                        'length' => 1,
                        'comment' => '1-available ,0-used',
                )
        );

        $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8'
        );

        $this->createTable( 'couponcode', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'couponcode' );
    }
}
