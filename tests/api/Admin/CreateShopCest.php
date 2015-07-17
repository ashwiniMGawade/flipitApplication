<?php
namespace Admin;

use \ApiTester;

class CreateShopCest
{
    public function testCreateShop(ApiTester $I)
    {
        $params = array(
            'name'                  => '',
            'permaLink'             => 'Mock',
            'overriteTitle'         => 'Mock',
            'metaDescription'       => 'Mock',
            'usergenratedcontent'   => 1,
            'discussions'           => 1,
            'title'                 => 'Mock',
            'subTitle'              => 'Mock',
            'notes'                 => 'Mock',
            'accountManagerName'    => 'Mock',
            'affliateNetwork'       => 'ddfdfdf',
            'deepLinkStatus'        => 1,
            'refUrl'                => 'Mock',
            'actualUrl'             => 'Mock',
            'shopText'              => 'Mock',
        );
        $I->wantTo('Create shop');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/shops', array('body'=>json_encode($params)));
        //$I->seeResponseCodeIs(400);
        //$I->seeResponseIsJson();
        //$I->seeResponseContainsJson(array('name' => 'dfdf'));
        $I->seeResponseContains('{"result":"ok"}');
    }
}
