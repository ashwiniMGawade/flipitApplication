<?php
namespace Admin;

use \ApiTester;

class UpdateVisitorCest
{
    public function testUpdateVisitorThrowsErrorWithEmptyParameters(ApiTester $I)
    {
        $params = '[]';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Invalid Parameters.'));
    }

    public function testUpdateVisitorThrowsErrorWithParameterEqualsString(ApiTester $I)
    {
        $params = 'SOME_INVALID_PARAMETER';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Invalid Parameters.'));
    }

    public function testUpdateVisitorThrowsErrorWithEmptyEventParameter(ApiTester $I)
    {
        $params = '[
                      {
                      }
                    ]';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Event Required'));
    }

    public function testUpdateVisitorThrowsErrorWithEventParameterEqualsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event":""
                        }
                    ]';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Event Required'));
    }

    public function testUpdateVisitorThrowsErrorWithMsgParameterDoesNotExist(ApiTester $I)
    {
        $params = '[
                        {
                            "event":"open"
                        }
                    ]';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Message Required'));
    }

    public function testUpdateVisitorThrowsErrorWithMsgParameterIsEmpty(ApiTester $I)
    {
        $params = '[
                        {
                            "event" : "open",
                            "msg" : ""
                        }
                    ]';
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Message Required'));
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
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Invalid Message or Message Parameters'));
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
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Invalid Message or Message Parameters'));
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
        $I->wantTo('Update Visitor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/visitors', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('msg' => 'Invalid Message or Message Parameters'));
    }

//    public function testUpdateVisitorThrowsErrorWithEmailIsInvalid(ApiTester $I)
//    {
//        $I->haveInDatabasePDOSite(
//            'visitors',
//            $params
//        );
//
//        $params = '[
//                        {
//                            "event" : "open",
//                            "msg" : {
//                                "email" : "test@example.com"
//                            }
//                        }
//                    ]';
//        $I->wantTo('Update Visitor');
//        $I->haveHttpHeader('Content-Type', 'application/json');
//        $I->sendPUT('/visitors', $params);
//        $I->seeResponseCodeIs(200);
//        $I->seeResponseIsJson();
//        $I->seeResponseContainsJson(array('msg' => 'Invalid Message or Message Parameters'));
//    }
}
