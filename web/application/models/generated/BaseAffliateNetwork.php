<?php
Doctrine_Manager::getInstance()->bindComponent('AffliateNetwork', 'doctrine_site');

/**
 * BaseAffliateNetwork
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $name
 * @property boolean $status
 * @property integer $replaceWithId
 * @property AffliateNetwork $affliatenetwork
 * @property Doctrine_Collection $shop
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseAffliateNetwork extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('affliate_network');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));

        $this->hasColumn('subId', 'string', 80, array(
                'type' => 'string',
                'length' => '80',
        ));


        $this->hasColumn('status', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('replaceWithId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to affliate_network.id , Defines a network is merged or not',
             'length' => '20',
             ));
        $this->hasColumn(
            'extendedSubid',
            'string',
            512,
            array(
                'type' => 'string',
                'length' => '512'
            )
        );
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('AffliateNetwork as affliatenetwork', array(
             'local' => 'replaceWithId',
             'foreign' => 'id'));

        $this->hasMany('Shop as shop', array(
             'local' => 'id',
             'foreign' => 'affliateNetworkId'));

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
