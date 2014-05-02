<?php
// Connection Component Binding

Doctrine_Manager::getInstance()->bindComponent('adminfavoriteshp', 'doctrine_site');

/**
 * BaseAdminfavoriteshp
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $shopId
 * @property integer $userId
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseAdminfavoriteshop extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('adminfavoriteshp');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('shopId', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('userId', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
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

        $this->hasMany('User as users', array(
                'local' => 'userId',
                'foreign' => 'id'));
        $this->hasMany('Shop as shops', array(
                'local' => 'shopId',
                'foreign' => 'id'));
    }
}
