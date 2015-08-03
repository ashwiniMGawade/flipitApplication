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

    public function signup(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Signup in this site');
        $this->stepsToSignup($I);
        $I->canSee('By creating this account you will subscribe to the Flipit newsletter');
        $I->seeLink('privacy policy');
        $I->seeLink('voorwaarden');
    }

    protected function stepsToSignup($I)
    {
        $I->amOnPage('/');
        $I->wait(5);
        $I->canSee('Inschrijven');
        $I->click('Inschrijven');
    }
}
