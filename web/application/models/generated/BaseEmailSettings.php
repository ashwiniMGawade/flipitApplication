<?php
Doctrine_Manager::getInstance()->bindComponent('EmailSettings', 'doctrine_site');

/**
 * BaseFooter
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $topFooter
 * @property string $middleColumn1
 * @property string $middleColumn2
 * @property string $middleColumn3
 * @property string $middleColumn4
 * @property string $bottomFooter
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseEmailSettings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('emailsettings');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('email', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
        $this->hasColumn('name', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
        $this->hasColumn('locale', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
        $this->hasColumn('timezone', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));

    }

    public function setUp()
    {
        parent::setUp();
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             ));
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
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
