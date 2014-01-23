<?php
Doctrine_Manager::getInstance()->bindComponent('ExcludedKeyword', 'doctrine_site');

/**
 * BaseExcludedKeyword
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $keyword
 * @property string $url
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseExcludedKeyword extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('excluded_keyword');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('keyword', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('url', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('action', 'enum', null, array(
        		'type' => 'enum',
        		'values' =>
        		array(
        				0 => 'redirect',
        				1 => 'connect',
        		),
        		'comment' => '0=redirect,1=connect',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('RefExcludedkeywordShop as shops', array(
        		'local' => 'id',
        		'foreign' => 'keywordid'));
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