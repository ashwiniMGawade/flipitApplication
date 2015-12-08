<?php
namespace Admin;

use \ApiTester;

class CreateShopCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

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
            'classification'        => 1,
        );
        $I->haveInDatabasePDOSite('affliate_network', array('name'=>'Affilinet'));
        $I->wantTo('Create shop');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/shops?api_key='.$this->apiKey, json_encode($params));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        //$I->seeResponseContainsJson(array('name' => 'Mock'));
        $I->seeResponseContainsJson(array('msg' => 'This operation is not permitted.'));
    }

    public function testCreateShopWithInvalidParams(ApiTester $I)
    {
        $params = array(
            'name' => ''
        );
        $I->wantTo('Create shop with invalid data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/shops?api_key='.$this->apiKey, json_encode($params));
        //$I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        //$I->seeResponseContainsJson(array('name' => array('This value should not be blank.')));
        $I->seeResponseContainsJson(array('msg' => 'This operation is not permitted.'));
    }
}
