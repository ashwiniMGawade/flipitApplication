<?php
//Doctrine_Manager::getInstance()->bindComponent('Shop', 'doctrine_site');

/**
 * BaseShop
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $totalViewcount
 * @property string $name
 * @property string $permaLink
 * @property string $metaDescription
 * @property string $notes
 * @property string $deepLink
 * @property boolean $deepLinkStatus
 * @property string $customHeader
 * @property string $refUrl
 * @property string $actualUrl
 * @property boolean $affliateProgram
 * @property string $overriteTitle
 * @property string $overriteSubtitle
 * @property string $overriteBrowserTitle
 * @property blob $shopText
 * @property integer $views
 * @property boolean $howToUse
 * @property boolean $showSignup
 * @property boolean $status
 * @property timestamp $offlineSicne
 * @property integer $accoutManagerId
 * @property integer $contentManagerId
 * @property integer $logoId
 * @property integer $howtoUseSmallImageId
 * @property integer $howtoUseBigImageId
 * @property integer $affliateNetworkId
 * @property integer $howtoUsepageId
 * @property boolean $showSimliarShops
 * @property boolean $showChains
 * @property integer $chainItemId id of chain item
 * @property Logo $logo
 * @property HowToUseSmallImage $howtousesmallimage
 * @property HowToUseBigImage $howtousebigimage
 * @property AffliateNetwork $affliatenetwork
 * @property Page $page
 * @property FavoriteShop $favoriteshops
 * @property Doctrine_Collection $category
 * @property Doctrine_Collection $offer
 * @property Doctrine_Collection $shopconversions
 * @property Doctrine_Collection $popularhop
 * @property Doctrine_Collection $refShopCategory
 *
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseShop extends Doctrine_Record
{

    public function setTableDefinition()
    {

        $this->setTableName('shop');
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
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('permaLink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('metaDescription', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('usergenratedcontent', 'enum', null, array(
                'type' => 'enum',
                'values' =>
                array(
                        0 => '0',
                        1 => '1',
                ),
         ));
        $this->hasColumn('customHeader', 'string', 1024, array(
                'type' => 'string',
                'length' => '1024',
        ));
        $this->hasColumn('notes', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('deepLink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('deepLinkStatus', 'boolean', null, array(
             'type' => 'boolean',
             ));

        $this->hasColumn('refUrl', 'string', 512, array(
             'type' => 'string',
             'length' => '512',
             ));
        $this->hasColumn('actualUrl', 'string', 512, array(
             'type' => 'string',
             'length' => '512',
             ));
        $this->hasColumn('showSimliarShops', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0
             ));

        $this->hasColumn('affliateProgram', 'boolean', null, array(
                'type' => 'boolean',
        ));

        $this->hasColumn('title', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));

        $this->hasColumn('subTitle', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));

        $this->hasColumn('overriteTitle', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('overriteSubtitle', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('overriteBrowserTitle', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('shopText', 'blob', null, array(
             'type' => 'blob',
             ));
        $this->hasColumn('views', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn('howToUse', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             ));

        $this->hasColumn('showSignupOption', 'boolean', null, array(
                'type' => 'boolean',
                'notnull' => true,
                'default' => 0
        ));
        $this->hasColumn('showChains', 'boolean', null, array(
                'type' => 'boolean',
                'notnull' => true,
                'default' => 0
        ));

        $this->hasColumn('strictConfirmation', 'boolean', null, array(
                'type' => 'boolean',
                'notnull' => true,
                'default' => 0
        ));

        $this->hasColumn('chainItemId', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('displayExtraProperties', 'boolean', null, array(
                'default' => 1 ,
                'type' => 'boolean',
        ));
        $this->hasColumn('ideal', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('qShops', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('freeReturns', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('pickupPoints', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('mobileShop', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('service', 'boolean', null, array(
                'default' => 0,
                'type' => 'boolean',
        ));
        $this->hasColumn('serviceNumber', 'string', 75, array(
             'type' => 'string',
             'length' => '75',
        ));
     /*   $this->hasColumn('Deliverytime', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('returnPolicy', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('freeDelivery', 'enum', null, array(
                'type' => 'enum',
                'values' =>
                array(
                        0 => '0',
                        1 => '1',
                        2 => '2',
                        3 => '3',
                ),
        ));
        $this->hasColumn('deliveryCost','decimal',8, array('scale' =>2));

       /* $this->hasColumn('deliveryCost', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));*/
        $this->hasColumn('status', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('offlineSicne', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('accoutManagerId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'associated account manager  id',
             'length' => '20',
             ));
        $this->hasColumn('accountManagerName', 'string', 5, array(
                'type' => 'string',
                'comment' => 'associated account manager  name',
                'length' => '5',
        ));
        $this->hasColumn('contentManagerId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'associated content  manager id',
             'length' => '20',
             ));
        /*
        $this->hasColumn('createdByName', 'string', 5, array(
                'type' => 'string',
                'comment' => 'associated account user  name',
                'length' => '5',
        ));
        $this->hasColumn('createdById', 'integer', 20, array(
                'type' => 'integer',
                'comment' => 'associated content  user id',
                'length' => '20',
        ));
        */
        $this->hasColumn('contentManagerName', 'string', 255, array(
                'type' => 'string',
                'comment' => 'associated account manager  name',
                'length' => '5',
        ));
        $this->hasColumn('logoId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id',
             'length' => '20',
             ));
        $this->hasColumn('screenshotId', 'integer', 20, array(
                'unique' => true,
                'type' => 'integer',
                'comment' => 'FK to image.id',
                'length' => '20',
        ));
        $this->hasColumn('howtoUseSmallImageId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id',
             'length' => '20',
             ));
        $this->hasColumn('howtoUseBigImageId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id',
             'length' => '20',
             ));
        $this->hasColumn('affliateNetworkId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK to affliate_network.id',
             'length' => '20',
             ));
        $this->hasColumn('howtoUsepageId', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'FK page.id',
             'length' => '20',
             ));
        $this->hasColumn('keywordlink', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
            ));
        $this->hasColumn('howtoTitle', 'string', null, array(
                'type' => 'string',
                'length' => null,
            ));
        $this->hasColumn('howtoSubtitle', 'string', null, array(
                'type' => 'string',
                'length' => null,
            ));
        $this->hasColumn('howtoMetaTitle', 'string', null, array(
                'type' => 'string',
                'length' => null,
            ));
        $this->hasColumn('howtoMetaDescription', 'blob', null, array(
                'type' => 'blob',
            ));
        $this->hasColumn('discussions', 'boolean', null, array(
                'type' => 'boolean',
        ));
        $this->hasColumn('addtosearch', 'boolean', null, array(
                'type' => 'boolean',
        ));
        $this->hasColumn('howToIntroductionText', 'blob', null, array(
                'type' => 'blob',
        ));

        $this->hasColumn('brandingcss', 'text', null, array(
             'type' => 'text',
        ));

        $this->hasColumn('lightboxfirsttext', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
        ));
        $this->hasColumn('lightboxsecondtext', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
        ));
        $this->hasColumn('customtext', 'blob', null, array(
             'type' => 'blob',
        ));
        $this->hasColumn('showcustomtext', 'boolean', null, array(
                'type' => 'boolean',
        ));
        $this->hasColumn('customtextposition', 'integer', 20, array(
                'type' => 'integer',
        ));
        $this->hasColumn('lastSevendayClickouts', 'integer', 20, array(
                'type' => 'integer',
        ));
        $this->hasColumn('shopAndOfferClickouts', 'integer', 20, array(
                'type' => 'integer',
        ));
        $this->hasColumn('shopsViewedIds', 'string', 100, array(
                'type' => 'string',
        ));
        $this->hasColumn('howtoSubSubTitle', 'string', 255, array(
            'type' => 'string'
        ));
        $this->hasColumn('moretextforshop', 'blob', null, array(
             'type' => 'blob'
        ));
        $this->hasColumn('howtoguideslug', 'string', 100, array(
            'type' => 'string',
            'length' => '100',
        ));
        $this->hasColumn(
            'futurecode',
            'boolean',
            null,
            array(
                'type' => 'boolean'
            )
        );
        $this->hasColumn('code_alert_send_date', 'timestamp', null, array(
            'type' => 'timestamp'
        ));
        $this->hasColumn('featuredtext', 'string', 255, array(
            'type' => 'string'
        ));
        $this->hasColumn('featuredtextdate', 'timestamp', null, array(
            'type' => 'timestamp'
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Logo as logo', array(
             'local' => 'logoId',
             'foreign' => 'id'));
        $this->hasOne('Image as smallimage', array(
                'local' => 'howtousesmallimageid',
                'foreign' => 'id'));
        $this->hasOne('Image as bigimage', array(
                'local' => 'howtousebigimageid',
                'foreign' => 'id'));
        $this->hasOne('WebsiteScrenshot as screenshot', array(
                'local' => 'screenshotId',
                'foreign' => 'id'));

        $this->hasOne('HowToUseSmallImage as howtousesmallimage', array(
             'local' => 'howtoUseSmallImageId',
             'foreign' => 'id'));

        $this->hasOne('HowToUseBigImage as howtousebigimage', array(
             'local' => 'howtoUseBigImageId',
             'foreign' => 'id'));

        $this->hasOne('AffliateNetwork as affliatenetwork', array(
             'local' => 'affliateNetworkId',
             'foreign' => 'id'));

        $this->hasOne('Page as page', array(
             'local' => 'howtoUsepageId',
             'foreign' => 'id'));

        $this->hasMany('Category as category', array(
             'refClass' => 'refShopCategory',
             'local' => 'shopId',
             'foreign' => 'categoryId'));

        $this->hasMany('Offer as offer', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('PopularShop as popularhop', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('refShopCategory', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('FavoriteShop as favoriteshops', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('Shop as relatedshops', array(
             'refClass' => 'refShopRelatedshop',
             'local' => 'shopId',
             'foreign' => 'relatedshopId'));

        $this->hasMany('refShopRelatedshop', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('Adminfavoriteshop as adminfevoriteshops', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('ExcludedKeyword as keywords', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('ShopHowToChapter as howtochapter', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $this->hasMany('Conversions as conversions', array(
             'local' => 'id',
             'foreign' => 'shopId'));

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             ));

        $this->hasMany('ShopViewCount as viewcount', array(
                'local' => 'id',
                'foreign' => 'shopid'));

        $this->hasMany('ShopReasons as shopreasons', array(
                'local' => 'id',
                'foreign' => 'shopid'));

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
