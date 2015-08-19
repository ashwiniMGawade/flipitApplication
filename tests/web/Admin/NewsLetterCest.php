<?php
namespace Admin;

use \WebTester;

class NewsLetterCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
    }

    public function testNewsLetterSubjectAcceptsApostrophe(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Test news letter subject accepts apostrophe');
        $I->amOnPage('admin/accountsetting/emailcontent');
        $I->fillField("#emailSubject", "Test's apostrophe");
        $I->click('#senderName');
        $I->amOnPage('admin/accountsetting/emailcontent');
        $I->seeInField("#emailSubject", "Test's apostrophe");
    }
}