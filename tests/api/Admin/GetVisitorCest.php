<?php
namespace Admin;

use \ApiTester;

class GetVisitorCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testGetVisitorReturnsDataWhenRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when record exists');
        $this->seedVisitorsTable($I);
        $I->sendGet('/visitors/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetVisitorGivesErrorWhenIdNotPassed(ApiTester $I)
    {
        $I->wantTo('Test GET visitor gives error when id does not passed.');
        $I->sendGet('/visitors/?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => 'Not found']);
    }

    public function testGetVisitorGivesErrorWhenInvalidIdPassed(ApiTester $I)
    {
        $I->wantTo('Test GET visitor gives error when invalid id passed');
        $I->sendGet('/visitors/test?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Invalid visitor Id')]);
    }

    public function testGetVisitorGivesErrorWhenRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor gives error when record does not exists');
        $I->sendGet('/visitors/0?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Visitor not found')]);
    }

    private function seedVisitorsTable($I)
    {
        $I->haveInDatabasePDOSite(
            'visitor',
            array(
                'id' => 1,
                'email' => 'test@example.com',
                'mailOpenCount' => 1,
                'mailClickCount' => 1,
                'mailSoftBounceCount' => 1,
                'mailHardBounceCount' => 1
            )
        );
    }
}
