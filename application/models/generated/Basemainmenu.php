<?php
Doctrine_Manager::getInstance()->bindComponent('mainmenu', 'doctrine_site');

/**
 * Basemainmenu
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property string $name
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class Basemainmenu extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('mainmenu');
           $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
           $this->hasColumn('parentId', 'integer', null, array(
                   'type' => 'integer',
                   'default' => null
           ));
        $this->hasColumn('iconId', 'integer', null, array(
                'type' => 'integer',
                'default' => null
        ));
        $this->hasColumn('url', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('position', 'integer', null, array(
                'type' => 'integer'
            ));
    }

    public function setUp()
    {
        parent::setUp();
        $nestedset0 = new Doctrine_Template_NestedSet(array(
             'hasManyRoots' => true,
             'rootColumnName' => 'root_id',
             ));
        $this->hasOne('Image as mainMenuIcon', array(
                'local' => 'iconId',
                'foreign' => 'id'));
        $this->actAs($nestedset0);
    }
}
