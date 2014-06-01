<?php
Doctrine_Manager::getInstance()->bindComponent('MoneysavingArticle', 'doctrine_site');

/**
 * BaseMoneysavingArticle
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property enum $type
 * @property integer $position
 * @property boolean $status
 * @property integer $offerId
 * @property Offer $offer
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.kundal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseMoneysavingArticle extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('moneysaving_article');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('type', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'MN',
              1 => 'AT',
             ),
             'comment' => 'AT – Automatic popularity, MN – Manual popularity',
             ));
        $this->hasColumn('position', 'integer', null, array(
             'type' => 'integer',
             'comment' => 'Holds the code position among popular code list',
             ));
        $this->hasColumn('status', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'comment' => '1 – enable , 0 – disable',
             ));
        $this->hasColumn('articleId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to offer.id',
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Articles as article', array(
             'local' => 'articleId',
             'foreign' => 'id'));

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
