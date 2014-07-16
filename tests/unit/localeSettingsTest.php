<?php

class localeSettingsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testLocaleUpdate()
    {
        $localeSettings = new LocaleSettings();
        $localeSettings->savelocale('nl_NL');
        $this->tester->canSeeInTable(
            'LocaleSettings',
            array(
                'locale' => 'nl_NL',
            )
        );
    }
    public function testTimezoneUpdate()
    {
        $localeSettings = new LocaleSettings();
        $localeSettings->saveTimezone('Europe/Amsterdam');
        $this->tester->canSeeInTable(
            'LocaleSettings',
            array(
                'timezone' => 'Europe/Amsterdam',
            )
        );
    }
}

