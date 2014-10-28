<?php
namespace admin;
use \FunctionalTester;

class localeSettingsCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function localeSettingsUpdate(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login('kim@web-flight.nl', 'Mind@123');
        $I->canSee('Locale Settings');
        $I->click('Locale Settings');
        $I->amOnPage('/admin/locale/locale-settings');
        $I->canSee('Locale Status');
        $I->click('Offline');
        $I->amOnPage('/admin/locale/locale-settings');
        $I->click('Online');
        $I->amOnPage('/admin/locale/locale-settings');
        $I->canSee('Locale');
        $I->canSee('Time Zone');
    }

    public function test(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
         $em = \Codeception\Module\Doctrine2::$em;
        $t =  $I->haveInRepository('KC\Entity\Settings', array('name' => 'test'));
        $I->persistEntity(
            new \KC\Entity\Settings,
            array(
            'name' => 'test',
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'deleted' => 0,
            'value' => 123
            )
        );
        $test = $I->grabFromRepository('KC\Entity\Settings', 'value', array('name' => 'test'));

        $em->getRepository( 'KC\Entity\Settings' )->findOneBy( array( 'name' => 'test' ) );
    }
}
