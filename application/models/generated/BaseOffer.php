<?php
Doctrine_Manager::getInstance()->bindComponent('Offer', 'doctrine_site');

/**
 * BaseOffer
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $totalViewcount
 * @property decimal $popularityCount
 * @property string $title
 * @property enum $Visability
 * @property enum $discountType
 * @property string $couponCode
 * @property string $refOfferUrl
 * @property string $refURL
 * @property string $discount
 * @property timestamp $startDate
 * @property timestamp $endDate
 * @property boolean $exclusiveCode
 * @property boolean $editorPicks
 * @property boolean $extendedOffer
 * @property string $extendedTitle
 * @property string $extendedUrl
 * @property string $extendedMetaDescription
 * @property blob $extendedFullDescription
 * @property integer $shopId
 * @property integer $offerLogoId
 * @property Shop $shop
 * @property Logo $logo
 * @property Doctrine_Collection $category
 * @property Doctrine_Collection $page
 * @property Doctrine_Collection $refOfferPage
 * @property Doctrine_Collection $termandcondition
 * @property Doctrine_Collection $popularcode
 * @property Doctrine_Collection $viewcount
 * @property Doctrine_Collection $refOfferCategory
 * @property integer $tilesId
 * @property boolean $shopExist
 * @property enum $copuonCodeTypeExist
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseOffer extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('offer');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
       $this->hasColumn('totalViewcount', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));

       $this->hasColumn('popularityCount', 'decimal', 16, array(
               'type' => 'decimal',
               'length' => '16',
               'scale' => 4
       ));



        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('Visability', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'DE',
              1 => 'MEM',
             ),
             'comment' => 'DF for default and MEM for members only',
             ));
        $this->hasColumn('discountType', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'CD',
              1 => 'SL',
              2 => 'PA',
              3 => 'NW',
             ),
             'comment' => 'CD-Code ,SL – Sale,PA – Printable',
             ));


        $this->hasColumn('couponCodeType', 'enum', null, array(
                'type' => 'enum',
                'values' =>
                array(
                        0 => 'GN',
                        1 => 'UN'
                ),
                'comment' => 'GN-general ,UN-unique',
        ));


        $this->hasColumn('couponCode', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('refOfferUrl', 'string', 512, array(
             'type' => 'string',
             'comment' => 'Ref. offer logo URL for printable discount type',
             'length' => '512',
             ));
        $this->hasColumn('refURL', 'string', 512, array(
             'type' => 'string',
             'length' => '512',
             ));
        $this->hasColumn('startDate', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('endDate', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('exclusiveCode', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             ));
        $this->hasColumn('editorPicks', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             ));
        $this->hasColumn('extendedOffer', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             ));
        $this->hasColumn('extendedTitle', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('extendedUrl', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('extendedMetaDescription', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('extendedFullDescription', 'blob', null, array(
             'type' => 'blob',
             ));
         $this->hasColumn('discount', 'string', 255, array(
             'type' => 'string',
             'length' => 255
             ));
        $this->hasColumn('discountvalueType', 'enum', 1, array(
             'type' => 'enum',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'values' =>  array(
                  0 => '0',
                  1 => '1',
                  2 => '2',
             ),
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('authorId', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('authorName', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('shopId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to shop.id',
             'length' => '20',
             ));
      $this->hasColumn('offerLogoId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id , Offer Logo for printable discount type',
             'length' => '20',
             ));
        $this->hasColumn('userGenerated', 'bolean', 20, array(
                 'type' => 'bolean',
        ));
        $this->hasColumn('approved', 'bolean', 20, array(
                'type' => 'bolean',
        ));
        $this->hasColumn('offline', 'bolean', 20, array(
                'type' => 'bolean',
        ));

        $this->hasColumn('maxlimit', 'enum', 1, array(
                'type' => 'enum',
                'length' => 1,
                'fixed' => false,
                'unsigned' => false,
                'values' =>
                array(
                        0 => '0',
                        1 => '1',
                ),
                'primary' => false,
                'default' => '0',
                'notnull' => true,
                'autoincrement' => false,
        ));

        $this->hasColumn('maxcode', 'integer', 11, array(
                'type' => 'integer',
                'length' => '11',
        ));

        $this->hasColumn('tilesId', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('shopExist', 'bolean', 20, array(
                'type' => 'bolean',
        ));

    }

    public function setUp()
    {
        parent::setUp();

        $this->hasOne('Shop as shop', array(
             'local' => 'shopId',
             'foreign' => 'id'));

        $this->hasMany('Vote as vote', array(
                'local' => 'id',
                'foreign' => 'offerId'));

        $this->hasOne('Logo as logo', array(
             'local' => 'offerLogoId',
             'foreign' => 'id'));

        $this->hasMany('Category as category', array(
             'refClass' => 'refOfferCategory',
             'local' => 'offerId',
             'foreign' => 'categoryId'));

        $this->hasMany('Page as page', array(
             'refClass' => 'refOfferPage',
             'local' => 'offerId',
             'foreign' => 'pageId'));

        $this->hasMany('refOfferPage as op', array(
             'local' => 'id',
             'foreign' => 'offerId'));

        $this->hasMany('TermAndCondition as termandcondition', array(
             'local' => 'id',
             'foreign' => 'offerId'));

        $this->hasMany('PopularCode as popularcode', array(
             'local' => 'id',
             'foreign' => 'offerId'));

        $this->hasMany('ViewCount as viewcount', array(
             'local' => 'id',
             'foreign' => 'offerId'));

        $this->hasMany('Conversions as conversions', array(
                'local' => 'id',
                'foreign' => 'offerId'));

        $this->hasMany('OfferNews as offernews', array(
                'local' => 'id',
                'foreign' => 'offerId'));


        $this->hasMany('CouponCode as codes', array(
                'local' => 'id',
                'foreign' => 'offerId'));


        $this->hasMany('refOfferCategory', array(
             'local' => 'id',
             'foreign' => 'offerId'));

        $this->hasOne('OfferTiles as tiles', array(
                'local' => 'tilesId',
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
