<?php
namespace Admin;

use \ApiTester;

class GetVisitorsCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testGetVisitorReturnsDataWhenRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when record exists');
        $this->seedVisitorsTable($I, 'test@example.com');
        $I->sendGet('/visitors?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('email' => 'test@example.com'));
    }

    public function testGetVisitorReturnsDataWhenPassedEmailRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when passed email record exists');
        $this->seedVisitorsTable($I, 'test@example.com');
        $I->sendGet('/visitors?email=test@example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('email' => 'test@example.com'));
    }

    public function testGetVisitorReturnsDataWhenPassedEmailRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when passed email record does not exists');
        $I->sendGet('/visitors?email=test@example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->cantSeeResponseContainsJson(array('email' => 'test@example.com'));
    }

    public function testGetVisitorReturnsDataWhenPassedEmailIdIsNotValid(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when passed email is not valid');
        $I->sendGet('/visitors?email=not-email&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('messages' => array( 'Invalid Email')));
    }

    private function seedVisitorsTable($I, $email)
    {
        $I->haveInDatabasePDOSite(
            'visitor',
            array(
                'email' => $email
            )
        );
    }
}
