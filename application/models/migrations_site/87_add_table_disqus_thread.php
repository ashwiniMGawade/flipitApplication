<?php
class AddTableDisqusThread extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
            'id' => array(
                'type'     => 'integer',
                'length'   => 10,
                'notnull'  => 1
            ),
            'title' => array(
                'type'   => 'string',
                'length' => 255,
                'notnull'  => 1
            ),
            'link' => array(
                'type'   => 'string',
                'length' => 255,
                'notnull'  => 1
            ),
            'created' => array(
                'type'   => 'integer',
                'length' => 10,
                'notnull'  => 1
            )
        );

        $options = array(
            'type'    => 'INNODB',
            'charset' => 'utf8'
        );
        $this->createTable('disqus_thread', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('disqus_thread');
    }
}
