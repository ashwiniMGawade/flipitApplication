<?php
namespace Admin;

use Symfony\Component\Validator\Constraints\DateTime;
use \WebTester;

class WidgetCest
{
    public function WidgetListingTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $this->seedWidgetTable($I);
        $I->login();
        $I->click('Widgets');
        $I->seeInTitle('Widgets List');
        $I->waitForText('My Test Widget');
    }

    public function createWidgetTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->amOnPage('/admin/widget');
        $I->click('backend_Add New Widget');
        $this->fillForm($I);
        $I->canSee('Widget has been added successfully');
        $I->canSee('Functional test widget');
    }

    public function editWidgetTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $this->seedWidgetTable($I);
        $I->login();
        $I->click('Widgets');
        $I->seeInTitle('Widgets List');
        $I->waitForText('My Test Widget');
        $I->click('My Test Widget');
        $I->fillField('#title', 'Functional test widget');
        $I->click('button#widgetSubmit');
        $I->canSee('Widget has been updated successfully');
        $I->canSee('Functional test widget');
    }

    private function seedWidgetTable($I)
    {
        $createdTime = new \DateTime();
        $endDate = new \DateTime();;
        $endDate->add(new \DateInterval('P10D'));
        $I->haveInDatabasePDOSite(
            'widget',
            array(
                'title' => 'My Test Widget',
                'status' => 1,
                'deleted' => 0,
                'showwithdefault' => 1,
                'created_at' => $createdTime->format('Y-m-d H:i:s'),
                'created_at' => $createdTime->format('Y-m-d H:i:s'),
                'startDate' => $createdTime->format('Y-m-d H:i:s'),
                'endDate' => $endDate->format('Y-m-d H:i:s')
            )
        );
    }

    protected function fillForm($I)
    {
        $startDate = date('d-m-Y');
        $endDate = date("d-m-Y", time()+86400);
        $I->waitForElementVisible('#widgetSubmit');
        $I->fillField('#title', 'Functional test widget');
        $I->click('#id_dated_yes');
        $I->executeJS('$("#widgetStartDate").val("'.$startDate.'")');
        $I->executeJS('$("#widgetEndDate").val("'.$endDate.'")');
        $I->executeJS('$("#content").val("Value")');
        $I->click('button#widgetSubmit');
    }
}
