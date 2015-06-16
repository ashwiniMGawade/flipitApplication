<?php
class AddIndexOnForeignKeyTables  extends Doctrine_Migration_Base
{
    public function up()
    {
        # create index on all table which don't have index on foreign key column
        $this->addIndex( 'visitor_keyword', 'visitorId',
                array(
                    'fields' => array(
                        'visitorId' => array()
                ))
        );

        $this->addIndex( 'adminfavoriteshp', 'shopId',
                    array(
                        'fields' => array(
                            'shopId' => array()
                    ))
        );

        $this->addIndex( 'articleviewcount', 'articleid',
                    array(
                        'fields' => array(
                            'articleid' => array()
                    ))
        );

        $this->addIndex( 'couponcode', 'offerid',
                    array(
                        'fields' => array(
                        '	offerid' => array()
                    ))
        );

        $this->addIndex( 'favorite_offer', 'offer_visitor_id',
                    array(
                        'fields' => array(
                            'offerId' => array(),
                            'visitorId' => array()
                    ))
        );

        $this->addIndex( 'favorite_shop', 'shop_visitor_id',
                    array(
                        'fields' => array(
                            'shopId' => array(),
                            'visitorId' => array()
                    ))
        );

        $this->addIndex( 'ref_article_store', 'article_shop_id',
                    array(
                        'fields' => array(
                            'articleid' => array(),
                            'storeid' => array()
                    ))
        );

        $this->addIndex( 'ref_excludedkeyword_shop', 'keyword_shop_id',
                    array(
                        'fields' => array(
                            'keywordid' => array(),
                            'shopid' => array()
                    ))
        );

        $this->addIndex('ref_offer_category', 'offer_category_id',
                    array(
                        'fields' => array(
                            'categoryid' => array(),
                            'offerid' => array()
                    ))
        );

        $this->addIndex('ref_offer_page', 'offer_page_id',
                    array(
                        'fields' => array(
                            'pageid' => array(),
                            'offerid' => array()
                    ))
        );

        $this->addIndex('ref_shop_category', 'shop_category_id',
                    array(
                        'fields' => array(
                            'shopid' => array(),
                            'categoryid' => array()
                    ))
        );

        $this->addIndex('ref_shop_relatedshop', 'shop_relatedshop_id',
                    array(
                        'fields' => array(
                            'shopId' => array(),
                            'relatedshopId' => array()
                    ))
        );

        $this->addIndex('route_permalink', 'permalink',
                    array(
                        'fields' => array(
                            'permalink' => array()
                    ))
        );


        $this->addIndex('route_redirect', 'orignalurl',
                    array(
                        'fields' => array(
                            'orignalurl' => array('length' => '512')
                    ))
        );


        $this->addIndex('shopviewcount', 'shopid',
                    array(
                        'fields' => array(
                            'shopid' => array()
                    ))
        );

        $this->addIndex('view_count', 'offer_click_count',
                    array(
                        'fields' => array(
                            'offerid' => array(),
                            'onclick' => array(),
                            'counted' => array(),
                    )),
                'index'
        );




    }

    public function down()
    {
        $this->removeIndex( 'visitor_keyword', 'visitorId');
        $this->removeIndex( 'adminfavoriteshp', 'shopId');
        $this->removeIndex( 'articleviewcount', 'articleid');
        $this->removeIndex( 'couponcode', 'offerid');
        $this->removeIndex( 'favorite_offer', 'offer_visitor_id');
        $this->removeIndex( 'favorite_shop', 'shop_visitor_id');
        $this->removeIndex( 'ref_article_store', 'article_shop_id');
        $this->removeIndex( 'ref_excludedkeyword_shop', 'keyword_shop_id');
        $this->removeIndex( 'ref_offer_category', 'offer_category_id');
        $this->removeIndex( 'ref_offer_page', 'offer_page_id');
        $this->removeIndex( 'ref_shop_category', 'shop_category_id');
        $this->removeIndex( 'ref_shop_relatedshop', 'shop_relatedshop_id');
        $this->removeIndex( 'route_permalink', 'permalink');
        $this->removeIndex( 'route_redirect', 'orignalurl');
        $this->removeIndex( 'shopviewcount', 'shopid');
        $this->removeIndex( 'view_count', 'offer_click_count');

    }
}
