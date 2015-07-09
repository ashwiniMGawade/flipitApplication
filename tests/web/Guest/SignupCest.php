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
        $I->canSee('Subscribe to newsletter');
        $this->fillForm($I);
        $I->click('input[type=submit]');
    }

    protected function stepsToSignup($I)
    {
        $I->amOnPage('/');
        $I->wait(5);
        $I->canSee('Inschrijven');
        $I->click('Inschrijven');
    }

    protected function fillForm($I)
    {
        $I->fillField('#emailAddress', 'kraj@web-flight.nl');
        $I->fillField('#password', 'password');
        $I->fillField('#firstName', 'kraj');
        $I->fillField('#lastName', 'test');
        $I->checkOption('#subscribeNewsLetter');
        $I->seeCheckboxIsChecked('#subscribeNewsLetter');
    }
}
