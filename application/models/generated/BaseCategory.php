<?php
Doctrine_Manager::getInstance()->bindComponent('Category', 'doctrine_site');

/**
 * BaseCategory
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $name
 * @property string $permaLink
 * @property string $metaDescription
 * @property blob $description
 * @property boolean $status
 * @property integer $categoryIconId
 * @property CategoryIcon $categoryicon
 * @property Doctrine_Collection $shop
 * @property Doctrine_Collection $offer
 * @property Doctrine_Collection $refShopCategory
 * @property Doctrine_Collection $refOfferCategory
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseCategory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('category');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('permaLink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('metatitle', 'string', null, array(
              'type' => 'string',
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
        ));
        $this->hasColumn('metaDescription', 'string', null, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('description', 'blob', null, array(
             'type' => 'blob',
             ));
        $this->hasColumn('status', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('categoryIconId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id',
             'length' => '20',
             ));
        $this->hasColumn('featured_category', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('categoryFeaturedImageId', 'integer', 20, array(
            'unique' => true,
            'type' => 'integer',
            'comment' => 'FK to image.id',
            'length' => '20',
         ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CategoryIcon as categoryicon', array(
             'local' => 'categoryIconId',
             'foreign' => 'id'));
        $this->hasOne('CategoryIcon as categoryfeaturedimage', array(
             'local' => 'categoryFeaturedImageId',
             'foreign' => 'id'));
        $this->hasMany('Shop as shop', array(
             'refClass' => 'refShopCategory',
             'local' => 'categoryId',
             'foreign' => 'shopId'));

        $this->hasMany('Offer as offer', array(
             'refClass' => 'refOfferCategory',
             'local' => 'categoryId',
             'foreign' => 'offerId'));

        $this->hasMany('refShopCategory', array(
             'local' => 'id',
             'foreign' => 'categoryId'));

        $this->hasMany('refOfferCategory', array(
             'local' => 'id',
             'foreign' => 'categoryId'));

        $this->hasMany('Articlecategory as articlecategory', array(
                'refClass' => 'RefArticlecategoryRelatedcategory',
                'local' => 'relatedcategoryid',
                'foreign' => 'articlecategoryid'));

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
