<?php
Doctrine_Manager::getInstance()->bindComponent('TermAndCondition', 'doctrine_site');

/**
 * BaseTermAndCondition
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $content
 * @property integer $offerId
 * @property Offer $offer
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseTermAndCondition extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('term_and_condition');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '20',
             ));
        $this->hasColumn('content', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('offerId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to offer.id',
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Offer as offer', array(
             'local' => 'offerId',
             'foreign' => 'id'));

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             'hardDelete' => true
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
