<?php
Doctrine_Manager::getInstance()->bindComponent('Page', 'doctrine_site');

/**
 * BasePage
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $pageType
 * @property string $pageTitle
 * @property string $permaLink
 * @property string $slug
 * @property string $metaTitle
 * @property string $metaDescription
 * @property blob $content
 * @property boolean $publish
 * @property boolean $pageLock
 * @property integer $pageAttributeId
 * @property boolean $enableTimeConstraint
 * @property integer $timenumberOfDays
 * @property integer $timeType
 * @property integer $timeMaxOffer
 * @property boolean $timeOrder
 * @property boolean $enableWordConstraint
 * @property string $wordTitle
 * @property integer $wordMaxOffer
 * @property boolean $wordOrder
 * @property boolean $enableAwardConstraint
 * @property string $awardType
 * @property string $customHeader
 * @property integer $awardMaxOffer
 * @property boolean $awardOrder
 * @property boolean $enableClickConstraint
 * @property integer $numberOfClicks
 * @property integer $clickMaxOffer
 * @property boolean $clickOrder
 * @property boolean $couponRegular
 * @property boolean $couponEditorPick
 * @property boolean $couponExclusive
 * @property boolean $saleRegular
 * @property boolean $saleEditorPick
 * @property boolean $saleExclusive
 * @property boolean $printableRegular
 * @property boolean $printableEditorPick
 * @property boolean $printableExclusive
 * @property boolean $showPage
 * @property PageAttribute $pageattribute
 * @property Doctrine_Collection $offer
 * @property Doctrine_Collection $widget
 * @property Doctrine_Collection $shop
 * @property Doctrine_Collection $refPageWidget
 * @property Doctrine_Collection $refOfferPage
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BasePage extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('page');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('pageType', 'string', 10, array(
             'type' => 'string',
             'comment' => 'specifies page either Default or OfferPage.',
             'length' => '10',
             ));
        $this->hasColumn('pageTitle', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('slug', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('permaLink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('metaTitle', 'string', null, array(
             'type' => 'string'
             ));
        $this->hasColumn('metaDescription', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('content', 'blob', null, array(
             'type' => 'blob',
             ));
        $this->hasColumn('publish', 'boolean', null, array(
             'type' => 'boolean',
             'comment' => 'defines page is published',
             ));
        $this->hasColumn('pageLock', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'comment' => 'defines page is locked',
             ));
        $this->hasColumn('showsitemap', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'comment' => 'defines show in sitemap or not',
             ));
        $this->hasColumn('pageAttributeId', 'integer', 11, array(
             'type' => 'integer',
             'comment' => 'Fk to page_attribute.id',
             'length' => '11',
             ));
        $this->hasColumn('contentManagerId', 'integer', 11, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('contentManagerName', 'integer', 11, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('enableTimeConstraint', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('timenumberOfDays', 'integer', 5, array(
             'type' => 'integer',
             'length' => '5',
             ));
        $this->hasColumn('timeType', 'integer', 5, array(
             'type' => 'integer',
             'comment' => '0 - no option selected',
             'length' => '5',
             ));
        $this->hasColumn('timeMaxOffer', 'integer', 5, array(
             'type' => 'integer',
             'length' => '5',
             ));
        $this->hasColumn('timeOrder', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('enableWordConstraint', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('wordTitle', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('customHeader', 'string', 1024, array(
                'type' => 'string',
                'length' => '1024',
        ));
        $this->hasColumn('wordMaxOffer', 'integer', 5, array(
             'type' => 'integer',
             'length' => '5',
             ));
        $this->hasColumn('wordOrder', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('awardConstratint', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('awardType', 'string', 5, array(
             'type' => 'string',
             'comment' => '0 - no option selected',
             'length' => '5',
             ));
        $this->hasColumn('awardMaxOffer', 'integer', 5, array(
             'type' => 'integer',
             'length' => '5',
             ));
        $this->hasColumn('awardOrder', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('enableClickConstraint', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('numberOfClicks', 'integer', 20, array(
             'type' => 'integer',
             'length' => '20',
             ));
        $this->hasColumn('clickMaxOffer', 'integer', 5, array(
             'type' => 'integer',
             'length' => '5',
             ));
        $this->hasColumn('clickOrder', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('maxOffers', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('oderOffers', 'string', 10, array(
                'type' => 'string',
                'length' => '10',
        ));
        $this->hasColumn('couponRegular', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('couponEditorPick', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('couponExclusive', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('saleRegular', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('saleEditorPick', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('saleExclusive', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('printableRegular', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('printableEditorPick', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('printableExclusive', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('showPage', 'boolean', null, array(
             'type' => 'boolean',
             'comment' => 'Show as page when creating offers.',
             ));
        $this->hasColumn('publishDate', 'timestamp', null, array(
                'type' => 'timestamp',
        ));

        $this->hasColumn('logoId', 'integer', 20, array(
                'unique' => true,
                'type' => 'integer',
                'comment' => 'FK to image.id',
                'length' => '20',
        ));

        $this->hasColumn('pageHeaderImageId', 'integer', 20, array(
                'unique' => true,
                'type' => 'integer',
                'comment' => 'FK to image.id',
                'length' => '20',
        ));

        $this->setSubClasses(array(
             'DefaultPage' =>
             array(
              'pageType' => 'default',
             ),
             'OfferListPage' =>
             array(
              'pageType' => 'offer',
             ),
             ));
        $this->hasColumn('offersCount', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('showinmobilemenu', 'boolean', null, array(
         'type' => 'boolean',
        ));
    }

    public function setUp()
    {
        parent::setUp();
         // ...
     /*   $this->actAs('Sluggable', array(
                'unique'    => true,
                'fields'    => array('pageTitle'),
                'canUpdate' => true
            )
        ); */
        $this->hasOne('Logo as logo', array(
                'local' => 'logoId',
                'foreign' => 'id'));

        $this->hasOne('Logo as pageheaderimage', array(
                'local' => 'pageHeaderImageId',
                'foreign' => 'id'));

        $this->hasOne('PageAttribute as pageattribute', array(
             'local' => 'pageAttributeId',
             'foreign' => 'id'));

        $this->hasMany('Offer as offer', array(
             'refClass' => 'refOfferPage',
             'local' => 'pageId',
             'foreign' => 'offerId'));

        $this->hasMany('Widget as widget', array(
             'refClass' => 'refPageWidget',
             'local' => 'pageId',
             'foreign' => 'widgetId'));

        $this->hasMany('Shop as shop', array(
             'local' => 'id',
             'foreign' => 'howtoUsepageId'));

        $this->hasMany('refPageWidget', array(
             'local' => 'id',
             'foreign' => 'pageId'));

        $this->hasMany('refOfferPage', array(
             'local' => 'id',
             'foreign' => 'pageId'));

        $this->hasMany('MoneySaving as moneysaving', array(
                'local' => 'id',
                'foreign' => 'pageid'));



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
