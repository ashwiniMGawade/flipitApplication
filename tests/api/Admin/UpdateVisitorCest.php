<?php
namespace Admin;

use \ApiTester;

class UpdateVisitorCest
{
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

    public function testUpdateVisitorThrowsErrorWithMsgParameterDoesNotExist(ApiTester $I)
    {
        $params = '[
                        {
                            "event":"open"
                        }
                    ]';
        $expectedResult = array('msg' => 'Message Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithMsgParameterIsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "msg" : ""
                        }
                    ]';
        $expectedResult = array('msg' => 'Message Required');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithMsgParameterIsInvalid(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "I_am_invalid" : ""
                            }
                        }
                    ]';
        $expectedResult = array('msg' => 'Invalid Message or Message Parameters');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailIsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : ""
                            }
                        }
                    ]';

        $expectedResult = array('msg' => 'Invalid Message or Message Parameters');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailIsInvalid(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : "invalid_email"
                            }
                        }
                    ]';

        $expectedResult = array('msg' => 'Invalid Message or Message Parameters');
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUpdateVisitorThrowsErrorWithEmailDoesNotExist(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "click",
                            "msg" : {
                                "email" : "test@example.com"
                            }
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
                            "msg" : {
                                "email" : "test@example.com"
                            }
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
                            "msg" : {
                                "email" : "test@example.com"
                            }
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
                            "msg" : {
                                "email" : "test@example.com"
                            }
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
                            "msg" : {
                                "email" : "test@example.com"
                            }
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

    public function testUsecaseThrowsErrorWhenOpensIsNotArray(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": "NOT_ARRAY"
                            }
                        }
                    ]';
        $expectedResult = array (
            'msg' => 'Invalid Message or Message Parameters'
        );
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseThrowsErrorWhenOpensIsEmpty(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": []
                            }
                        }
                    ]';
        $expectedResult = array (
            'msg' => 'Invalid Message or Message Parameters'
        );
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseThrowsErrorWhenOpensDoesNotContainTimestamp(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": [
                                    {
                                        "something":"invalid"
                                    }
                                ]
                            }
                        }
                    ]';
        $expectedResult = array (
            'msg' => 'Invalid Opens Timestamp'
        );
        $status = 405;
        $this->runTest($I, $params, $expectedResult, $status);
    }

    public function testUsecaseThrowsErrorWhenOpensTimestampIsNotAnInteger(ApiTester $I)
    {
        $this->seedVisitorsTable($I);
        $params = '[
                        {
                            "event" : "open",
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": [
                                    {
                                        "ts":"NOT_AN_INTEGER"
                                    }
                                ]
                            }
                        }
                    ]';
        $expectedResult = array (
            'msg' => 'Invalid Opens Timestamp'
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
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": [
                                    {
                                        "ts": "1430805793"
                                    }
                                ]
                            }
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

    public function testUsecaseReturnsErrorWhenValidationFails(ApiTester $I)
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
                            "msg" : {
                                "email" : "test@example.com",
                                "opens": [
                                    {
                                        "ts": "1365111111"
                                    }
                                ]
                            }
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

    private function runTest($I, $params, $expectedResult, $status)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs($status);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($expectedResult);
    }
}
