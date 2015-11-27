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
        $I->sendGet('/visitors?test@email=example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('email' => 'test@example.com'));
    }

    public function testGetVisitorReturnsDataWhenPassedEmailRecordNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitor returns data when passed email record not exists');
        $I->sendGet('/visitors?test@email=example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->cantSeeResponseContainsJson(array('email' => 'test@example.com'));
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
