<?php
Doctrine_Manager::getInstance()->bindComponent('ViewCount', 'doctrine_site');

/**
 * BaseViewCount
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $loadTime
 * @property integer $onClick
 * @property integer $onHover
 * @property string $IP
 * @property integer $offerId
 * @property integer $memberId
 * @property boolean $counted
 * @property Offer $offer
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseViewCount extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('view_count');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('loadTime', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn('onClick', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));

        $this->hasColumn('onLoad', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('onHover', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn('IP', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));

        $this->hasColumn('counted', 'boolean', null, array(
                'type' => 'boolean',
                'default' =>  0
        ));

        $this->hasColumn('offerId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to offer.id',
             'length' => '20',
             ));
        $this->hasColumn('memberId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK of member who view offer (used for future reference)',
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Offer as offer', array(
             'local' => 'offerId',
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
