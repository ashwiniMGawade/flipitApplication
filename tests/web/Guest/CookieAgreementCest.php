<?php
namespace Guest;
use \WebTester;

class CookieAgreementCest
{
    public function cookieAgreementTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Sign cookie usage agreement');
        $I->amOnPage('/');
        $I->canSee('This site uses cookies');
        $I->seeLink('Okay, thanks');
        $I->click('Okay, thanks');
        $I->reloadPage();
        $I->cantSee('This site uses cookies');
        $I->cantSeeLink('Okay, thanks');
    }

}
