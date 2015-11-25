<?php
namespace Admin;

use \ApiTester;

class UpdateVisitorCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = '%25NWcIzZ6Oy9uXv7fKJBZE!5%24EEMN%245%26X';
    }

    public function testUpdateVisitorThrowsErrorWithEmptyParameters(ApiTester $I)
    {
        $params = '[]';
        $expectedResult = array('msg' => 'Invalid Parameters.');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithParameterEqualsString(ApiTester $I)
    {
        $params = 'SOME_INVALID_PARAMETER';
        $expectedResult = array('msg' => 'Invalid Parameters.');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmptyEventParameter(ApiTester $I)
    {
        $params =   '[
                      {
                      }
                    ]';
        $expectedResult = array('msg' => 'Event Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEventParameterEqualsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event":""
                        }
                    ]';
        $expectedResult = array('msg' => 'Event Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailParameterDoesNotExist(ApiTester $I)
    {
        $params = '[
                        {
                            "event":"open"
                        }
                    ]';
        $expectedResult = array('msg' => 'Email Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailParameterIsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "email" : ""
                        }
                    ]';
        $expectedResult = array('msg' => 'Email Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailIsInvalid(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "email" : "invalid_email"
                        }
                    ]';

        $expectedResult = array('msg' => 'Invalid Email');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEventIsInvalid(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "invalid-event",
                            "email" : "test@example.com"
                        }
                    ]';

        $expectedResult = array('msg' => 'Invalid Event');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseUpdatesTheEmailClickCountWhenEventEqualsClick(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "click",
                            "email" : "test@example.com"
                        }
                    ]';
        $expectedResult = array (
            'test@example.com' =>
                array (
                    'open' => 1,
                    'click' => 2,
                    'soft_bounce' => 1,
                    'hard_bounce' => 1,
                ),
        );
        $status = 200;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseUpdatesTheEmailSoftBounceCountWhenEventEqualsSoftBounce(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "soft_bounce",
                            "email" : "test@example.com"
                        }
                    ]';
        $expectedResult = array (
            'test@example.com' =>
                array (
                    'open' => 1,
                    'click' => 1,
                    'soft_bounce' => 2,
                    'hard_bounce' => 1,
                ),
        );
        $status = 200;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseUpdatesTheEmailHardBounceCountWhenEventEqualsHardBounce(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "hard_bounce",
                            "email" : "test@example.com"
                        }
                    ]';
        $expectedResult = array (
            'test@example.com' =>
                array (
                    'open' => 1,
                    'click' => 1,
                    'soft_bounce' => 1,
                    'hard_bounce' => 2,
                ),
        );
        $status = 200;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseThrowsErrorWhenOpensEventDoesNotContainTimestamp(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "email" : "test@example.com"
                        }
                    ]';
        $expectedResult = array (
            'msg' => 'Invalid mail open Timestamp'
        );
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseUpdatesTheEmailOpenCountWhenEventEqualsOpen(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "email" : "test@example.com",
                            "timeStamp": "1430805793"
                        }
                    ]';
        $expectedResult = array (
            'test@example.com' =>
                array (
                    'open' => 2,
                    'click' => 1,
                    'soft_bounce' => 1,
                    'hard_bounce' => 1,
                ),
        );
        $status = 200;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    /*public function testUsecaseReturnsErrorWhenValidationFails(ApiTester $I)
    {
        $I->haveInDatabasePDOSite(
            'visitor',
            array(
                'id' => 1,
                'email' => 'test@example.com',
                'mailOpenCount' => 1,
                'mailClickCount' => 1,
                'mailSoftBounceCount' => 1,
                'mailHardBounceCount' => 1,
                'active' => 1234234
            )
        );

        $params = '[
                        {
                            "event" : "open",
                            "email" : "test@example.com",
                            "timeStamp": "1365111111"
                        }
                    ]';
        $expectedResult = array (
            'active' =>
                array (
                    0 => 'This value should have exactly 1 character.',
                ),
        );

        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }*/

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

    private function runTest($I, $params, $expectedResult, $status)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors?api_key='.$this->apiKey, $params);
        $I->seeResponseCodeIs($status);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($expectedResult);
    }
}
