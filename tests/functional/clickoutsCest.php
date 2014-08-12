<?php
use \FunctionalTester;

class clickoutsCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function clickout(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.icon a');
        $I->switchToWindow();
        $I->seeInCurrentUrl('/in');
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.header-block-2 .box');
        $I->switchToWindow();
        $I->seeInCurrentUrl('/in');
        $I->amOnPage('/in/acceptance-shop');
        $I->maximizeWindow();
        $I->click('.web a');
        $I->switchToWindow();
        $I->seeInCurrentUrl('/in');
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.offer-holder .buttons > a');
        $I->executeInSelenium(function (\Webdriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->wait(10);
        $I->canSeeInPageSource('id="code-lightbox"');
        $I->canSeeInPageSource('id="code-button"');
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.line a');
        $I->switchToWindow();
    }
}