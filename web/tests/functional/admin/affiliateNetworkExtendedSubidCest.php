<?php
namespace admin;
use \FunctionalTester;

class affiliateNetworkExtendedSubidCest
{
    public function _before()
    {

    }

    public function _after()
    {
    }

    public function extendedNetworkSubidFieldCheck(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Winkels');
        $I->click('Winkels');
        $I->canSee('Affiliate Netwerken');
        $I->click('Affiliate Netwerken');
        $I->click('#createNetwork');
        $I->amOnPage('admin/affiliate/addaffiliate');
        $I->canSee('backend_Extended Subid');
    }

    public function addAffiliateNetwork(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->amOnPage('admin/affiliate/addaffiliate');
        $I->fillField('input[name=addNetworkText]', 'test Network');
        $I->fillField('input[name=subId]', 'ssss');
        $I->fillField('input[name=extendedSubid]', '1234');
        $I->click('#addNetwork');
        $I->amOnPage('admin/affiliate');
    }

    public function updateAffiliateNetwork(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->amOnPage('admin/affiliate/editaffiliate/id/1');
        $I->fillField('input[name=addNetworkText]', 'test Network1');
        $I->click('#updatenetrecord');
        $I->amOnPage('admin/affiliate');
    }
}
