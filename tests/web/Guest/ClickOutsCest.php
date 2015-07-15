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
        $I = new WebTester($scenario);
        $this->commonClickouts($I, 'a.store-header-link');
    }

    public function offerImageClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->commonClickouts($I, '.offer-holder a');
    }

    public function offerTitleClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->commonClickouts($I, '.clickout-title');
    }

    public function expiredClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->switchOfferClickouts('expired', '.line a', '', $I);
    }

    public function couponCodeClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->switchOfferClickouts('couponCode', '.offer-teaser-button-wrapper-inner a', $I);
    }

    public function saleClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->switchOfferClickouts('sale', '.btn-blue-wrapper a', $I);
    }
    
    public function printableClickout(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester($scenario);
        $this->switchOfferClickouts('printable', 'Bekijk de kortingsbon', $I);
    }

    protected function commonClickouts($I, $cssClassName)
    {
        $I->amOnPage('acceptance-shop');
        $I->click($cssClassName);
        $I->switchToWindow();
        $I->seeInCurrentUrl('/');
    }

    protected function switchOfferClickouts($codeType, $cssClassName, $I)
    {
        switch ($codeType) {
            case 'couponCode':
                $this->commonOfferClickouts($cssClassName, $I);
                $I->canSee('test');
                break;
            case 'sale':
                $this->commonOfferClickouts($cssClassName, $I);
                $I->seeInCurrentUrl('/');
                $I->wait(5);
                $I->amOnPage('acceptance-shop');
                break;
            case 'printable':
                $I->amOnPage('acceptance-shop');
                $I->click($cssClassName);
                $I->seeInCurrentUrl('/');
                break;
            default:
                break;
        }
    }

    protected function commonOfferClickouts($cssClassName, $I)
    {
        $I->amOnPage('acceptance-shop');
        $I->click($cssClassName);
        $I->wait(10);
    }
}
