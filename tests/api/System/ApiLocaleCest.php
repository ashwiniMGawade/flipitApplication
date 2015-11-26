<?php
namespace System;

use \ApiTester;

class ApiLocaleCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testAccessingApiWithKCUrl(ApiTester $I)
    {
        $I->wantTo('Try to access API with KC URL');
        $I->sendGet('/shops/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testAccessingApiWithFlipitUrlAndLocaleParam(ApiTester $I)
    {
        $I->wantTo('Try to access API with Flipit url and locale param');
        $I->sendGet('http://api.dev.flipit.com/in/shops/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testAccessingApiWithFlipitUrlAndWithoutLocaleParam(ApiTester $I)
    {
        $I->wantTo('Try to access API with Flipit url and without locale param');
        $I->sendGet('http://api.dev.flipit.com/shops/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('message'=>'Not found'));
    }
}
