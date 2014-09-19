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

    // tests
    public function testCodeAlertEmailSubject()
    {
        $codeAlertSettings = new CodeAlertSettings();
        $codeAlertEmailValue = array('val' => 'test');
        $codeAlertSettings->saveCodeAlertEmailSubject($codeAlertEmailValue);
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
        $codeAlertHeaderValue = array('data' => 'test');
        $codeAlertSettings->saveCodeAlertEmailHeader($codeAlertHeaderValue);
        $this->tester->canSeeInTable(
            'CodeAlertSettings',
            array(
                'email_header' => 'test'
            )
        );
    }

    public function testScheduleEmailSave()
    {
        $codeAlertSettings = new CodeAlertSettings();
        $codeAlertScheduleParameters = Zend_Controller_Front::getInstance();
        $codeAlertScheduleParameters->setParam('isScheduled', '1');
        $codeAlertScheduleParameters->setParam('sendDate', '09-17-2014');
        $codeAlertScheduleParameters->setParam('sendTime', '12.00');
        $codeAlertScheduleParameters->setParam('timezone', '');
        $codeAlertSettings->saveScheduledNewsletter($codeAlertScheduleParameters);
        $this->tester->canSeeInTable(
            'CodeAlertSettings',
            array(
                'code_alert_schedule' => '1',
                'code_alert_status' => '0',
                'code_alert_schedule_time' => '2014-09-17 12:00:00',
            )
        );
    }

    public function testScheduleEmailDisable()
    {
        $codeAlertSettings = new CodeAlertSettings();
        $codeAlertScheduleParameters = Zend_Controller_Front::getInstance();
        $codeAlertScheduleParameters->setParam('isScheduled', '1');
        $codeAlertScheduleParameters->setParam('sendDate', '09-17-2014');
        $codeAlertScheduleParameters->setParam('sendTime', '12.00');
        $codeAlertScheduleParameters->setParam('timezone', '');
        $codeAlertSettings->saveScheduledNewsletter($codeAlertScheduleParameters);
        $codeAlertSettings->updateCodeAlertSchedulingStatus();
        $this->tester->canSeeInTable(
            'CodeAlertSettings',
            array(
                'code_alert_schedule' => '0',
                'code_alert_status' => '1',
            )
        );
    }
}
