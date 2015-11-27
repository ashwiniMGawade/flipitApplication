<?php
namespace Admin;

use \ApiTester;

class UpdateVisitorCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testPutVisitorGivesErrorWhenInvalidIdPassed(ApiTester $I)
    {
        $I->wantTo('Test PUT visitor gives error when invalid id passed');
        $this->seedVisitorsTable($I);
        $params = json_encode(array('email' => 'ramesh@example.com'));
        $expectedResult = array('messages' => array( 'Visitor not found'));
        $status = 404;
        $this->runTest($I, 2, $params, $expectedResult, $status);
    }

    public function testPutVisitorGivesErrorWhenIdNotSpecified(ApiTester $I)
    {
        $I->wantTo('Test PUT visitor gives error when id does not passed');
        $I->sendPUT('/visitors/?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => 'Not found']);
    }

    public function testPutVisitorUpdatesVisitorWhenValidInputPassed(ApiTester $I)
    {
        $I->wantTo('Test PUT visitor updates visitor when valid input passed');
        $this->seedVisitorsTable($I);
        $params = json_encode(array('email' => 'test@example.com'));
        $expectedResult = array('email' => 'test@example.com');
        $status = 200;
        $this->runTest($I, 1, $params, $expectedResult, $status);
    }

    public function testPutVisitorGivesErrorWhenInValidEmailPassed(ApiTester $I)
    {
        $I->wantTo('Test PUT visitor gives error when invalid email passed');
        $this->seedVisitorsTable($I);
        $params = json_encode(array('email' => 'not an email'));
        $expectedResult = array('This value is not a valid email address.');
        $status = 405;
        $this->runTest($I, 1, $params, $expectedResult, $status);
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

    private function runTest($I, $id, $params, $expectedResult, $status)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors/'.$id.'?api_key='.$this->apiKey, $params);
        $I->seeResponseCodeIs($status);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($expectedResult);
    }
}
