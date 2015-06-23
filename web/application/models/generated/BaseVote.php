<?php
Doctrine_Manager::getInstance()->bindComponent('Vote', 'doctrine_site');

/**
 * BaseVote
 *
 *
 * @property integer $id
 * @property string $offerId
 * @property enum $Visability
 * @property enum $discountType
 * @property string $couponCode
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##RAMAN## <##rkumar1@seasiaconsulting.com##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseVote extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('votes');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('offerId', 'integer', 11, array(
                'type' => 'integer',
                'length' => '11'
        ));
        $this->hasColumn('ipAddress', 'string', 200, array(
                'type' => 'string',
                'length' => '200'
        ));
        $this->hasColumn('date', 'timestamp', null, array(
                'type' => 'timestamp',
        ));
        $this->hasColumn('vote', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('moneySaved', 'double', 255, array(
                'type' => 'double',
                'length' => '255'
        ));
        $this->hasColumn('product', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('status', 'integer', 11, array(
                'type' => 'integer',
                'length' => '11'
        ));
        $this->hasColumn('visitorId', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20'
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasOne('Offer as offer', array(
                'local' => 'offerId 	',
                'foreign' => 'id'));

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
                'name' => 'deleted',
                'type' => 'boolean'

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
