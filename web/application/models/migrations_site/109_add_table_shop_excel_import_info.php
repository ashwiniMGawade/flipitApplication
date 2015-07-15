<?php
class AddTableShopExcelImportInfo extends Doctrine_Migration_Base
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
            'totalShopsCount' => array(
                'type'   => 'integer',
                'length' => 10

            ),
            'passCount' => array(
                'type'   => 'integer',
                'length' => 10

            ),
            'failCount' => array(
                'type'   => 'integer',
                'length' => 10

            ),
            'deleted' => array(
                'type'   => 'boolean',
                'default' => 0,
                'length' => 1,
                'comment' => '0-on ,1-off'
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
        $this->createTable('shopExcelInformation', $columns, $options);
    }

    public function down()
    {
        $this->dropTable('shopExcelInformation');
    }
}
