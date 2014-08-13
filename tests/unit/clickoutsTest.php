<?php


class clickoutsTest extends \Codeception\TestCase\Test
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

    // tests
    public function testMe()
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
    }

}