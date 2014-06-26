<?php
namespace admin;
use Codeception\Util\Stub;

class emailSettingTest extends \Codeception\TestCase\Test
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

    public function testGetEmailSettings()
    {
        $emailSettings = new Settings();
        $campain = array('sender' => 'd@d.com','subject' => 'koek');
        $save = $emailSettings->getEmailSettings('sender_email_address');
       // $this->tester->seeInTable('Settings', array('sender' => 'd@d.com','subject' => 'koek'));
    }
}
