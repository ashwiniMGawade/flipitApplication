<?php
namespace Guest;
use \WebTester;

class SignupCest
{
    public function _before(WebTester $I)
    {
    }

    public function _after(WebTester $I)
    {
    }

    public function signupTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Signup in this site');
        $I->amOnPage('/in');
        $I->canSee('Flipit.com');
        $I->amOnPage('/in/subscription');
        $I->canSee('Subscribe to newsletter');
    }
}
