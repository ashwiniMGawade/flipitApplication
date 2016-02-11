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

    public function testGetVisitorsReturnsVisitorsWhenRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitors returns visitors when record exists');
        $this->seedVisitorsTable($I, 'test@example.com');
        $I->sendGet('/visitors?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('email' => 'test@example.com'));
    }

    public function testGetVisitorsReturnsVisitorsWhenPassedEmailRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitors returns visitors when passed email record exists');
        $this->seedVisitorsTable($I, 'test@example.com');
        $I->sendGet('/visitors?&filter[where][email]=test@example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('email' => 'test@example.com'));
    }


    public function testGetVisitorsReturnsEmptyWhenPassedEmailRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET visitors returns empty when passed email record does not exists');
        $I->sendGet('/visitors?&filter[where][email]=somethingelse@example.com&api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->cantSeeResponseContainsJson(array('email' => 'somethingelse@example.com'));
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
