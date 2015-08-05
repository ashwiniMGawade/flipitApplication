<?php
namespace Admin;

use \WebTester;

class PopularCodesCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
    }

    public function createPopularCodeAndCheckIfRecommended(WebTester $I, \Codeception\Scenario $scenario)
    {
        $startDate = date('Y-m-d H:i:s', time()-86400);
        $endDate = date('Y-m-d H:i:s', time()+86400);

        $I->wantTo('See top-20 offers are not recommended');
        /* Insert an offer in a database */
        $I->haveInDatabasePDOSite(
            'offer',
            array(
                'id' => '100',
                'title' => 'New most popular offer code',
                'visability' => 'DE',
                'discounttype' => 'CD',
                'couponcode' => 'OFFER100',
                'startdate' => $startDate,
                'enddate' => $endDate,
                'exclusivecode' => '0',
                'editorpicks' => '0',
                'extendedoffer' => '0',
                'extendedtitle' => '',
                'extendedurl' => '',
                'extendedmetadescription' => '',
                'extendedfulldescription' => '',
                'discount' => '0',
                'discountvalueType' => '0',
                'authorId' => '1',
                'authorName' => 'Some Author',
                'shopid' => '1',
                'maxlimit' => '',
                'maxcode' => '0',
                'deleted' => '0',
                'created_at' => '2015-06-30 17:01:34',
                'updated_at' => '2015-06-30 17:01:34',
                'userGenerated' => '0',
                'approved' => '0',
                'offline' => '0',
                'tilesId' => '331',
                'shopexist' => '1',
                'popularityCount' => '0',
                'couponcodetype' => 'GN',
                'extendedoffertitle' => ''
            )
        );

        /* Go to popular offers page & create one*/
        $I->canSee('Top Kortingscodes');
        $I->click('Top Kortingscodes');
        $I->click('Search a offer');
        $I->canSee('New most popular offer code');
        $I->click('li.select2-highlighted');
        $I->click('button#addNewOffer');

        /*Come back to offer's page & check it shouldn't recommended*/
        $I->amOnPage('admin/offer');
        $I->waitForText('OFFER100');
        $I->click('OFFER100');
        $I->seeInCurrentUrl('admin/offer/editoffer/id/100');
        $I->click('#optionsOnbtn');
        $I->cantSeeCheckboxIsChecked('#editorpickcheckbox');
    }

}