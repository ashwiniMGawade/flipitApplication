<?php
Doctrine_Manager::getInstance()->bindComponent('Conversions', 'doctrine_site');

/**
 * BaseConversions
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $offerId
 * @property integer $shopId 
 * @property integer $visitorId
 * @property string $IP
 * @property string $subid
 * @property string $utma
 * @property string $utmz
 * @property string $utmv
 * @property string utmx
 * @property Shop $shop
 * @property Offer $offer
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseConversions extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('conversions');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('IP', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('subid', 'string', 50, array(
        	 'type' => 'string',
        	 'length' => '50',
        	 ));
        $this->hasColumn('utma', 'string', 255, array(
        	 'type' => 'string',
        	 'length' => '255',
        	 ));
        $this->hasColumn('utmz', 'string', 255, array(
        	 'type' => 'string',
        	 'length' => '255',
        	 ));
        $this->hasColumn('utmv', 'string', 255, array(
        	 'type' => 'string',
        	 'length' => '255',
        	 ));
        $this->hasColumn('utmx', 'string', 255, array(
        	 'type' => 'string',
        	 'length' => '255',
        	 ));
        $this->hasColumn('converted', 'boolean', null, array(
        		'type' => 'boolean',
        		'default' => 0
        ));
        $this->hasColumn('shopId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to shop.id',
             'length' => '20',
        	 ));
        $this->hasColumn('offerId', 'integer', 20, array(
        		'type' => 'integer',
        		'comment' => 'FK to offer.id',
        		'length' => '20',
        ));
        $this->hasColumn('visitorId', 'integer', 20, array(
        		'type' => 'integer',
        		'comment' => 'FK to visitor.id',
        		'length' => '20',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Shop as shop', array(
             'local' => 'shopId',
             'foreign' => 'id'));
        
        $this->hasOne('Offer as offer', array(
        		'local' => 'offerId',
        		'foreign' => 'id'));
        
        $this->hasOne('Visitor as visitor', array(
        		'local' => 'visitorId',
        		'foreign' => 'id'));
        
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