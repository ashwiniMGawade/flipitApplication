<?php
class AddPopularArticlesTable extends Doctrine_Migration_Base
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
            'articleId' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'position' => array(
                'type'   => 'integer',
                'length' => 11
            ),
            'deleted' => array(
                'type'   => 'boolean',
                'length' => 1,
                'default' => 0
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
        $this->createTable('popular_articles', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('popular_articles');
    }
}
