<?php
class AddTableIpAddresses extends Doctrine_Migration_Base
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
                'name' => array(
                    'type'   => 'string',
                    'length'   => 255
                ),
                'ipaddress' => array(
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
        $this->createTable('ip_addresses', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('ip_addresses');
    }
}
