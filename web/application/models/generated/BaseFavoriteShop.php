<?php
Doctrine_Manager::getInstance()->bindComponent('FavoriteShop', 'doctrine_site');

/**
 * BaseFavoriteShop
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $shopId
 * @property integer $visitorId
 * @property Doctrine_Collection $user
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseFavoriteShop extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('favorite_shop');
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
        $this->hasColumn('visitorId', 'integer', 20, array(
            'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn(
            'code_alert_send_date',
            'timestamp',
            null,
            array(
                'type' => 'timestamp'
            )
        );

    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Visitor as visitors', array(
             'local' => 'visitorId',
             'foreign' => 'id'));

        $this->hasMany('Shop as shops', array(
                'local' => 'shopId',
                'foreign' => 'id'));

        $this->hasOne('signupfavoriteshop as favshop', array(
                'local' => 'shopId',
                'foreign' => 'store_id'));
/*
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             'options' =>
             array(
              'default' => 0,
             ),
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' =>
             array(
              'name' => 'created_at',
             )
             ));
        $this->actAs($softdelete0);
        $this->actAs($timestampable0)*/
    }
}