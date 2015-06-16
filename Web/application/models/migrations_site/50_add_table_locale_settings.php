<?php
class AddLocaleSettingsTable extends Doctrine_Migration_Base
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
            'locale' => array(
                    'type'   => 'string',
                    'length' => 10
            ),
            'timezone' => array(
                    'type'   => 'string',
                    'length' => 255
            )
        );

        $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8'
        );
        $this->createTable('locale_settings', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('locale_settings');
    }
}
