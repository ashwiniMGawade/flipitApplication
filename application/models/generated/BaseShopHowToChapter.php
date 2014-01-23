<?php
Doctrine_Manager::getInstance()->bindComponent('ShopHowToChapter', 'doctrine_site');

/**
 * BaserefOfferCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $offerId
 * @property integer $categoryId
 * @property Offer $Offer
 * @property Category $Category
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseShopHowToChapter extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shop_howto_chapter');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('shopId', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn('chapterTitle', 'string', null, array(
        		'type' => 'string',
        		'length' => '255',
        	));
        $this->hasColumn('chapterDescription', 'blob', null, array(
        		'type' => 'blob',
        	));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Shop as shop', array(
             'local' => 'shopId',
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