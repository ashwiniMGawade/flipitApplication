<?php
namespace Admin;

use \ApiTester;

class UpdateShopCest
{
    public function testUpdateShop(ApiTester $I)
    {
        $params = array(
            'name'                  => 'Mock',
            'permaLink'             => 'Mock',
            'overriteTitle'         => 'Mock',
            'metaDescription'       => 'Mock',
            'usergenratedcontent'   => 1,
            'discussions'           => 1,
            'title'                 => 'Mock',
            'subTitle'              => 'Mock',
            'notes'                 => 'Mock',
            'accountManagerName'    => 'Mock',
            'deepLinkStatus'        => 1,
            'refUrl'                => 'Mock',
            'actualUrl'             => 'Mock',
            'shopText'              => 'Mock',
        );
        $I->haveInDatabasePDOSite('shop', $params);
        $I->wantTo('Update shop');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/shops/1', json_encode($params));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Mock'));
    }

    public function testUpdateShopWithInvalidParams(ApiTester $I)
    {
        $params = array(
            'name'                  => ''
        );
        $I->wantTo('Update shop with invalid data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/shops/1', json_encode($params));
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => array('This value should not be blank.')));
    }

    public function testUpdateShopWithInvalidContentType(ApiTester $I)
    {
        $params = array(
            'name'                  => ''
        );
        $I->wantTo('Update shop with invalid content type');
        $I->haveHttpHeader('Content-Type', 'application/text');
        $I->sendPUT('/shops/1', json_encode($params));
        $I->seeResponseCodeIs(415);
    }
}
