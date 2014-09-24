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
    
    public function testUpdateSendersDetails()
    {
        $emailSettings = new Settings();
        $emailSettings->updateSendersSettings('sender_email_address', 'kim@web-flight.nl');
        $emailSettings->updateSendersSettings('sender_name', 'kim');
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => 'kim@web-flight.nl',
                'name' => 'sender_email_address'
            )
        );
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => 'kim',
                'name' => 'sender_name'
            )
        );
    }

    public function testGetSendersDetails()
    {
        $emailSettings = new Settings();
        $emailAddress = $emailSettings->getEmailSettings('sender_email_address');
        $senderName = $emailSettings->getEmailSettings('sender_name');
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => $emailAddress,
                'name' => 'sender_email_address'
            )
        );
        $this->tester->canSeeInTable(
            'Settings',
            array(
                'value' => $senderName,
                'name' => 'sender_name'
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
