<?php
class AddShopviewcountTable extends Doctrine_Migration_Base
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
                'shopid' => array(
                        'type'   => 'integer',
                        'length' => 20

                ),
                'onclick' => array(
                        'type'   => 'integer',
                        'length' => 20

                ),
                'onload' => array(
                        'type'   => 'integer',
                        'length' => 20

                ),
                'ip' => array(
                        'type'   => 'string',
                        'length' => 255

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

        $this->createTable( 'shopviewcount', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'shopviewcount' );
    }
}
