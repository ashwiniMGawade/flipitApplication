<?php
namespace Admin;
use \WebTester;

class RedirectsFilterCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
    }

    public function testRedirectSearch(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Test redirect search functionality.');

        $this->seedRedirectData($I, 10, 'www.xyz.com/index', 'www.xyz.com');

        $I->amOnPage('/admin/redirect');

        $I->wait(1);
        $I->canSee('www.xyz.com/index');
        $I->click('Search Redirect');
        $I->canSee('www.xyz.com');
        $I->fillField('input[type=text]', 'www.xy');
        $I->wait(1);
        $I->click('li.select2-highlighted');
        $I->click('#search_redirect_btn');
        $I->wait(1);
        $I->canSee('www.xyz.com/index');
    }



    private function seedRedirectData($I, $id, $originalUrl, $redirectToUrl)
    {
        $I->haveInDatabasePDOSite(
            'route_redirect',
            array(
                'id'            => $id,
                'orignalurl'    => $originalUrl,
                'redirectto'    => $redirectToUrl,
                'created_at'    => '2015-05-15 00:00:00',
                'updated_at'    => '2015-05-15 00:00:00',
                'deleted'       => 0,
            )
        );
    }
}
