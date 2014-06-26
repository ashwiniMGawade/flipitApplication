<?php
use Codeception\Util\Stub;

class emailSettingsTest extends \Codeception\TestCase\Test
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

    public function testUpdateSendersEmailAddress()
    {
        $emailSettings = new Settings();
        $emailSettings->updateSendersEmailAddress('kim@web-flight.nl');
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => 'kim@web-flight.nl',
                'name' => 'sender_email_address'
            )
        );
    }

    public function testGetSendersEmailAddress()
    {
        $emailSettings = new Settings();
        $emailAddress = $emailSettings->getEmailSettings('sender_email_address');
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => $emailAddress,
                'name' => 'sender_email_address'
            )
        );
    }
}
