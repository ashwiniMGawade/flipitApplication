<?php
class AddDisqusCommentsTable extends Doctrine_Migration_Base
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
            'comment_id' => array(
                    'type'   => 'integer',
                    'length' => 20
            ),
            'message' => array(
                    'type'   => 'string',
                    'length' => 255
            ),
            'page_title' => array(
                    'type'   => 'string',
                    'length' => 255
            ),
            'page_url' => array(
                    'type'   => 'string',
                    'length' => 255
            ),
            'created_at' => array(
                    'type'   => 'timestamp',
                    'length' => 12
            ),
            'author_name' => array(
                    'type'   => 'string',
                    'length' => 255
            ),
            'author_profile_url' => array(
                    'type'   => 'string',
                    'length' => 255
            ),
            'author_avtar' => array(
                    'type'   => 'string',
                    'length' => 255
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
