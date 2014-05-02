<?php
class AddEmailSettingsTable extends Doctrine_Migration_Base
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
                'email' => array(
                        'type'   => 'string'
                ),
                'name' => array(
                        'type'   => 'string'

                ),
                'locale' => array(
                        'type'   => 'string'

                ),
                'timezone' => array(
                        'type'   => 'string'

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

        $this->createTable( 'emailsettings', $columns, $options );

    }

    public function down()
    {
        $this->dropTable( 'emailsettings' );
    }
}
