<?php
use \FunctionalTester;

class helloworldCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function helloworldTest(FunctionalTester $I)
    {
        //$I->initializeDb('Db', $I->flipitTestDb());
        $I->amOnPage('http://dev.kortingscode.nl/');
        $I->see('op');
    }
}