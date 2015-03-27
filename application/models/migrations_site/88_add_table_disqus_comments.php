<?php
class AddTableDisqusComments extends Doctrine_Migration_Base
{
    public function up()
    {
        $columns = array(
            'id' => array(
                'type'     => 'integer',
                'length'   => 10,
                'notnull'  => 1
            ),
            'thread_id' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'author_name' => array(
                'type'   => 'string',
                'length' => 255
            ),
            'comment' => array(
                'type'   => 'string',
                'length' => 500
            ),
            'created' => array(
                'type'   => 'integer',
                'length' => 10
            )
        );
        $options = array(
            'type'    => 'INNODB',
            'charset' => 'utf8'
        );
        $this->createTable('disqus_comments', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('disqus_comments');
    }
}
