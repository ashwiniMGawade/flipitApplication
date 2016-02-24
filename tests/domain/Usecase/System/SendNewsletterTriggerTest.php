<?php
namespace Usecase\System;

use Core\Domain\Entity\BulkEmail;
use Core\Domain\Entity\LocaleSettings;
use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Usecase\System\SendNewsletterTrigger;

class SendNewsletterTriggerTest extends \Codeception\TestCase\Test
{
    protected $tester;
    protected $newsletterCampaign;
    protected $bulkEmail;
    protected $local;
    protected $bulkEmailRepositoryMock;
    protected $localeSettingsMock;

    public function _before()
    {
        $this->local = "en";

        $this->newsletterCampaign = new NewsletterCampaign();
        $this->newsletterCampaign->setCampaignName('Super Duper Campaign');
        $this->newsletterCampaign->setScheduledTime(new \DateTime('yesterday', (new \DateTimezone("Europe/Amsterdam"))));
        $this->newsletterCampaign->setId(1);
        $this->newsletterCampaign->setScheduledStatus(1);
        $newsletterCampaignScheduledTime = $this->newsletterCampaign->getScheduledTime();

        // Setting converting the time to UTC as Lambda works with this timezone.
        $newsletterCampaignScheduledTimeUTC = new \DateTime(
            $newsletterCampaignScheduledTime->format("d-m-Y\\TH:i:s"),
            (new \DateTimezone("UTC"))
        );

        $this->bulkEmail = new BulkEmail();
        $this->bulkEmail->setTimeStamp($newsletterCampaignScheduledTimeUTC->getTimestamp()*1000);
        $this->bulkEmail->setReferenceId($this->newsletterCampaign->getId());
        $this->bulkEmail->setEmailType('newsletter');
        $this->bulkEmail->setLocal($this->local);

        $this->bulkEmailRepositoryMock = $this->createBulkEmailRepositoryMock();
        $this->localeSettingsMock = $this->createLocaleSettingsMock();
    }

    public function testSendNewsletterTrigger()
    {
        $expectedNewsletterCampaign = clone $this->newsletterCampaign;
        $expectedNewsletterCampaign->setScheduledStatus(2);

        $bulkEmailRepositoryMock = $this->scheduleBulkEmailMock(
            $this->bulkEmailRepositoryMock,
            $this->bulkEmail
        );
        $sendNewsletterTrigger = new SendNewsletterTrigger($bulkEmailRepositoryMock, $this->localeSettingsMock, $this->local);

        $result = $sendNewsletterTrigger->execute($this->newsletterCampaign);
        $expectedResult = $this->newsletterCampaign->getCampaignName() . " (" . $this->newsletterCampaign->getId() . ") " . strtoupper($this->local);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSendNewsletterTriggerNotScheduled()
    {
        $scheduledStatusOptionsNotToBeSend = array(0, 2, 3);

        foreach ($scheduledStatusOptionsNotToBeSend as $scheduleStatus) {
            $notToBeScheduledNewsletterOnStatus = clone $this->newsletterCampaign;
            $notToBeScheduledNewsletterOnStatus->setScheduledStatus($scheduleStatus);

            $bulkEmailRepositoryMock = $this->dontScheduleBulkEmailMock($this->bulkEmailRepositoryMock);
            $sendNewsletterTrigger = new SendNewsletterTrigger($bulkEmailRepositoryMock, $this->localeSettingsMock, $this->local);

            $result = $sendNewsletterTrigger->execute($notToBeScheduledNewsletterOnStatus);
            $this->assertEquals('', $result);
        }
    }

    public function testSendNewsletterTriggerTimeHasNotComeYet()
    {
        $notToBeScheduledNewsletterOnScheduleTime = clone $this->newsletterCampaign;
        $notToBeScheduledNewsletterOnScheduleTime->setScheduledTime(new \DateTime('tomorrow'));

        $bulkEmailRepositoryMock = $this->dontScheduleBulkEmailMock($this->bulkEmailRepositoryMock);
        $sendNewsletterTrigger = new SendNewsletterTrigger($bulkEmailRepositoryMock, $this->localeSettingsMock, $this->local);

        $result = $sendNewsletterTrigger->execute($notToBeScheduledNewsletterOnScheduleTime);
        $this->assertEquals('', $result);
    }

    private function createBulkEmailRepositoryMock()
    {
        return $this->getMockBuilder('\Core\Domain\Repository\BulkEmailRepositoryInterface')->getMock();
    }

    private function scheduleBulkEmailMock($bulkEmailRepositoryMock, $expectedBulkEmail)
    {
        $bulkEmailRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($expectedBulkEmail)
            ->willReturn(null);
        return $bulkEmailRepositoryMock;
    }

    private function dontScheduleBulkEmailMock($bulkEmailRepositoryMock)
    {
        $bulkEmailRepositoryMock
            ->expects($this->never())
            ->method('save')
            ->willReturn(null);
        return $bulkEmailRepositoryMock;
    }

    private function createLocaleSettingsMock()
    {
        $localeSettingsObject = new LocaleSettings();
        $localeSettingsObject->setId(1);
        $localeSettingsObject->setLocale('nl_NL');
        $localeSettingsObject->setTimezone('Europe/Amsterdam');
        $localeSettings = array ($localeSettingsObject);

        $localeSettingsMock = $this->getMockBuilder('\Core\Domain\Repository\LocaleSettingRepositoryInterface')->getMock();
        $localeSettingsMock
            ->expects($this->any())
            ->method('findBy')
            ->willReturn($localeSettings);
        return $localeSettingsMock;
    }
}
