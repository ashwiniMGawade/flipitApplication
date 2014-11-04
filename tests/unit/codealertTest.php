<?php


class codealertTest extends \Codeception\TestCase\Test
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

    public function testCodeAlertEmailSubject()
    {
        $codeAlertSettings = new CodeAlertSettings();
        $codeAlertSettings->saveCodeAlertSettings('test', '');
        $this->tester->canSeeInTable(
            'CodeAlertSettings',
            array(
                'email_subject' => 'test'
            )
        );
    }

    public function testCodeAlertEmailHeader()
    {
        $codeAlertSettings = new CodeAlertSettings();
        $codeAlertSettings->saveCodeAlertSettings('', 'test');
        $this->tester->canSeeInTable(
            'CodeAlertSettings',
            array(
                'email_header' => 'test'
            )
        );
    }
}
