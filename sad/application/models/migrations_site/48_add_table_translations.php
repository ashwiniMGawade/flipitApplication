<?php
class AddTranslationsTable extends Doctrine_Migration_Base
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
            'translationKey' => array(
                    'type'   => 'string'
            ),
            'translation' => array(
                    'type'   => 'string'
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

        $this->createTable('translations', $columns, $options);

    }

    public function down()
    {
        $this->dropTable('translations');
    }
}
