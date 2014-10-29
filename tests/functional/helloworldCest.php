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
        $I->amOnPage('/admin/helloworld');
        $I->canSee('hello world');
        $I->canSee('Amit Kraj Rajbir');

    }
}