<?php
namespace admin;
use \FunctionalTester;

class testCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->amOnPage('/in');
        $I->canSee('testing');
    }
}