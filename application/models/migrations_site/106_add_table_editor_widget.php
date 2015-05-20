<?php
class AddTableEditorWidget extends Doctrine_Migration_Base
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
            'type' => array(
                'type'   => 'string',
                'length' => 255

            ),
            'description' => array(
                'type'   => 'string',
                'length' => 1024

            ),
            'subtitle' => array(
                'type'   => 'string',
                'length' => 255

            ),
            'editorId' => array(
                'type'   => 'integer',
                'length' => 10

            ),
            'status' => array(
                'type'   => 'boolean',
                'default' => 1,
                'length' => 1,
                'comment' => '1-on ,0-off'
            ),
            'created_at' => array(
                'type'   => 'timestamp'
            ),
            'updated_at' => array(
                'type'   => 'timestamp'
            )
        );
        $options = array(
            'type'    => 'INNODB',
            'charset' => 'utf8'
        );
        $this->createTable('editorwidget', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('editorwidget');
    }
}
