<?php
namespace Admin;

use \WebTester;

class LandingPagesCest
{
    public function createLandingPageTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->amOnPage('/admin/landingpages/create');
        $I->executeJS('jQuery("#selectedShop").val("1")');
        $I->fillField('permalink', 'my-url');
        $I->fillField('title', 'My Page Title');
        $I->click('#publishPageButton');
        $I->waitForText('Landing Page has been added successfully');
    }

    public function editLandingPageTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->haveInDatabasePDOSite(
            'landingPages',
            array(
                'id' => '1',
                'shopId' => '1',
                'title' => 'My Page Title',
                'permalink' => 'my-url',
                'subTitle' => '',
                'metaTitle' => '',
                'metaDescription' => '',
                'content' => null,
                'status' => '1',
                'offlineSince' => null,
                'createdAt' => '2015-09-16 15:04:19',
                'updatedAt' => '2015-09-16 15:04:19'
            )
        );
        $I->login();
        $I->amOnPage('/admin/landingpages/edit/id/1');
        $I->executeJS('jQuery("#selectedShop").val("1")');
        $I->fillField('permalink', 'my-url-edited');
        $I->fillField('title', 'My Page Title Edited');
        $I->click('#publishPageButton');
        $I->waitForText('Landing Page has been updated successfully');
    }
}
