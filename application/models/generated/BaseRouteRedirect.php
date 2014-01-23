<?php
Doctrine_Manager::getInstance()->bindComponent('RouteRedirect', 'doctrine_site');

/**
 * BaseRouteRedirect
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $orignalurl
 * @property string $redirectto
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##kraj## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseRouteRedirect extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('route_redirect');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        
        $this->hasColumn('orignalurl', 'string', null, array(
             'type' => 'string'
             ));
        $this->hasColumn('redirectto', 'string', null, array(
             'type' => 'string'
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
?>