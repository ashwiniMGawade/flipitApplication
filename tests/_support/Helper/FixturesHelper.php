<?php
namespace Helper;

class FixturesHelper
{
    public function execute($I)
    {
        $I->haveInDatabasePDOSite(
            'locale_settings',
            array(
                'locale'=>'nl_NL',
                'timezone'=>'Europe/Amsterdam'
            )
        );

        for ($i=1; $i < 5; $i++) {
            $I->haveInDatabasePDOSite(
                'category',
                array(
                    'name' => 'test cat'.$i,
                    'permalink' => 'test-cat'.$i
                )
            );
        }

        $I->haveInDatabasePDOSite(
            'image',
            array(
                'ext' => 'jpg',
                'type' => 'HTUB',
                'path' => 'images/upload/shop/',
                'name' => '1409026126_Jellyfish.jpg',
                'deleted' => 0
            )
        );

        $I->haveInDatabasePDOSite(
            'footer',
            array(
                'topfooter' => 'jpg',
                'middlecolumn1' => 'HTUB',
                'middlecolumn2' => 'images/upload/shop/',
                'middlecolumn3' => '1409026126_Jellyfish.jpg',
                'middlecolumn4' => 0,
                'bottomfooter' => 'dd'
            )
        );

        $I->haveInDatabasePDOSite(
            'shop',
            array(
                'name' => 'acceptance shop',
                'permalink' => 'acceptance-shop',
                'title' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'contentmanagerid' => '1',
                'affliateprogram' => 1,
                'refurl' => 'http://www.kortingscode.nl/',
                'actualurl' => 'http://www.kortingscode.nl/',
                'howtouse' => '1',
                'howtoTitle' => 'acceptance shop title',
                'howtoSubtitle' => 'acceptance shop title',
                'howtoMetaTitle' => 'acceptance shop title',
                'howtoMetaDescription' => 'acceptance shop title',
                'howtousesmallimageid' => 1,
                'howtousebigimageid' => 1,
                'status' => 1
            )
        );

        $I->haveInDatabasePDOSite(
            'ref_shop_category',
            array(
                'shopid' => '1',
                'categoryid' => '1'
            )
        );

        $I->haveInDatabasePDOSite(
            'route_permalink',
            array(
                'permalink' => 'acceptance-shop',
                'type' => 'SHP',
                'exactlink' => 'store/storedetail/id/1'
            )
        );

        $I->haveInDatabasePDOSite(
            'route_permalink',
            array(
                'permalink' => 'how-to/acceptance-shop',
                'type' => 'SHP',
                'exactlink' => 'store/howtoguide/shopid/1'
            )
        );

        $futureDate = new \DateTime();
        $futureDate->modify('+1 week');
        $futureDate = $futureDate->format('Y-m-d H:i:s');
        $pastDate = new \DateTime();
        $pastDate->modify('-1 week');
        $pastDate = $pastDate->format('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7));
        $I->haveInDatabasePDOSite(
            'offer',
            array(
                'shopid' => '1',
                'couponcode' => 'test',
                'tilesId' => '1',
                'title' => 'Topa',
                'created_at' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'updated_at' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'visability' => 'DE',
                'discounttype' => 'CD',
                'startdate' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'enddate' => $endDate,
                'authorId' => 1,
                'shopexist' => 1,
                'couponcodetype' => 'GN',
                'discountvalueType' => 'coupon code offer'
            )
        );

        $I->haveInDatabasePDOSite(
            'offer_tiles',
            array(
                'label' => 'test',
                'type' => 'CD',
                'ext' => 'png',
                'path' => 'images/upload/offertiles',
                'name' => 'test.png'
            )
        );

        $I->haveInDatabasePDOSite(
            'ref_offer_category',
            array(
                'offerid' => '1',
                'categoryid' => '1'
            )
        );

        $I->haveInDatabasePDOSite(
            'dashboard',
            array(
                'message' => 'jpg',
                'no_of_offers' => 12,
                'no_of_shops' => 3,
                'no_of_clickouts' => 321,
                'no_of_subscribers' => 0,
                'total_no_of_offers'=> 123,
                'total_no_of_shops'=> 22,
                'total_no_of_shops_online_code'=> 33,
                'total_no_of_shops_online_code_lastweek'=> 33,
                'total_no_members'=>33
            )
        );

        $I->haveInDatabasePDOSite(
            'page',
            array(
                'pagetype' => 'default',
                'pagetitle' => 'page',
                'permalink' => 'inschrijven',
                'metatitle' => 'inschrijven',
                'metadescription' => 'inschrijven',
                'content' => '',
                'publish'=>1,
                'pageattributeid'=>1,
                'contentManagerId'=>1,
                'contentManagerName'=>'test',
                'pageHeaderImageId'=>1
            )
        );
    }
}
