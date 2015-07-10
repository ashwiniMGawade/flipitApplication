<?php
namespace Guest;
use \WebTester;

class ClickOutsCest
{
    public function _before(WebTester $I)
    {
    }

    public function _after(WebTester $I)
    {
    }

    public function headerImageClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wait(10);
        $I = new WebTester($scenario);
        $this->commonClickouts($I, 'a.store-header-link');
    }

    /* public function sidebarClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->commonClickouts($I, '.web a');
    }*/

    public function headerLinkClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->commonClickouts($I, 'a.store-header-link');
    }



    protected function commonClickouts($I, $cssClassName)
    {
        $I->amOnPage('acceptance-shop');
        $I->click($cssClassName);
        $I->switchToWindow();
        $I->seeInCurrentUrl('/');
    }
}
