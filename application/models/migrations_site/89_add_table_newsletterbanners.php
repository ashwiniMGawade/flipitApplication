<?php
class AddTableNewsletterbanners extends Doctrine_Migration_Base
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
            'name' => array(
                'type'   => 'string',
                'length' => 255
            ),
            'path' => array(
                'type'   => 'string',
                'length' => 255
            ),
            'imagetype' => array(
                    'type'   => 'string',
                    'length' => 1
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
        $this->createTable('newsletterbanners', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('newsletterbanners');
    }
}