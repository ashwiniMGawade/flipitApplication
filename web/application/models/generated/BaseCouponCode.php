<?php

/**
 * BaseCouponCode
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property boolean $status
 * @property string $code
 * @property integer $offerid
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseCouponCode extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('couponcode');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));

        $this->hasColumn('offerid', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));

        $this->hasColumn('status', 'boolean', 1, array(
             'type' => 'boolean',
             'length' => '1',
             'default' => 1,
             'comment' => '1-available ,0-used',
             ));

        $this->hasColumn('code', 'string', 10, array(
                'type' => 'string',
                'length' => '10',
        ));



    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Shop as shop', array(
             'local' => 'shopid',
             'foreign' => 'id'));

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
                'name' => 'deleted',
                'type' => 'boolean',
                'options' =>
                array(
                        'default' => 0,
                ),
        ));


    }
}
