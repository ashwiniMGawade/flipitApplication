<?php
class AddApiKeysTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
                'id' => array(
                        'type'     => 'integer',
                        'length'   => 11,
                        'primary'  => 1,
                        'autoincrement' => 1,
                        'notnull'  => 1
                ),
                'user_id' => array(
                        'type'   => 'integer',
                        'foreign'=> 'id',
                        'foreignTable'	=>	'user',
                        'onDelete'     => 'CASCADE'
                ),
                'api_key' => array(
                        'type'   => 'varchar',
                        'length' =>	'32',
                        'notnull'  => 1
                ),
                'deleted' => array(
                        'type'   => 'boolean',
                        'length' => 1
                ),
                'created_at' => array(
                        'type'   => 'timestamp',
                        'length' => 12
                )
        );
        $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8'
        );
        $this->createTable('api_keys', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('api_keys');
    }
}
