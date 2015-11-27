<?php

class WelcomeCest
{
    public function welcomeTest(ApiTester $I)
    {
        $I->wantTo('Welcome API');
        $I->sendGet('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"messages":"Welcome to Flipit"}');
    }
}
