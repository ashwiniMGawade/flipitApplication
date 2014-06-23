<?php
use Codeception\Util\Stub;

class backend_emailsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

   public function testEmailSettings(CodeGuy $I)
    {
    	$I->wantToTest('Test email settings class.');
    	$user = new Emailsettings();
          	 
    }
    
    public function saveEmailSettings(CodeGuy $I)
    {
    	$I->wantToTest('Save email settings.');
    	$user = new Emailsettings();
    	$user->emailperlocale = 'test@flipit.com';
    	$user->sendername = 'flipit';
    	$user->save();
    	$this->codeGuy->seeInDatabase(
    	             'Signupmaxaccount',
    	             array('sendername' => 'flipit')
    	 );
          
    }
}