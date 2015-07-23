<?php
namespace Admin;

use \ApiTester;

class CreateShopCest
{
    public function testCreateShopWithValidParameters(ApiTester $I)
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
            'affliateNetwork'       => 'Affilinet',
            'deepLinkStatus'        => 1,
            'refUrl'                => 'Mock',
            'actualUrl'             => 'Mock',
            'shopText'              => 'Mock',
        );
        $I->haveInDatabasePDOSite('affliate_network', array('name'=>'Affilinet'));
        $I->wantTo('Create shop');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/shops', json_encode($params));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Mock'));
    }

    public function testCreateShopWithInvalidParams(ApiTester $I)
    {
        $params = array(
            'name' => ''
        );
        $I->wantTo('Create shop with invalid data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/shops', json_encode($params));
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => array('This value should not be blank.')));
    }
}
