<?php
Doctrine_Manager::getInstance()->bindComponent('GlobalExportPassword', 'doctrine');
/**
 * BaseGlobalExportPassword
 *
 * @property integer $id
 * @property string $password
 * @property string $exportType
 */
abstract class BaseGlobalExportPassword extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('global_export_password');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('password', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('exportType', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));

    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' =>
             array(
              'name' => 'created_at',
             ),
             'updated' =>
             array(
              'name' => 'updated_at',
             ),
             ));
        $this->actAs($timestampable0);
    }
}
