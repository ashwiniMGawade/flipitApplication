<?php
use Codeception\Util\Stub;

class backend_emailsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeTester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function saveEmailSettings(codeTester $I)
    {
        $I->wantToTest('Save email settings.');
        $user = new Emailsettings();
        $user->emailperlocale = 'test@flipit.com';
        $user->sendername = 'flipit';
        $user->save();
        $this->codeTester->seeInDatabase(
            'Signupmaxaccount',
            array('sendername' => 'flipit')
        );
          
    }
}