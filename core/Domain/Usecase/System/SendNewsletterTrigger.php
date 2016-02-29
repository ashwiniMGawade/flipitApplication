<?php

namespace Core\Domain\Usecase\System;

use Core\Domain\Repository\BulkEmailRepositoryInterface;
use Core\Domain\Repository\LocaleSettingRepositoryInterface;
use Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Entity\BulkEmail;

class SendNewsletterTrigger
{
    private $bulkEmailRepository;
    private $localeSettingsRepository;
    private $local;

    public function __construct(
        BulkEmailRepositoryInterface $bulkEmailRepository,
        LocaleSettingRepositoryInterface $localeSettingsRepository,
        $local
    ) {
        $this->bulkEmailRepository = $bulkEmailRepository;
        $this->localeSettingsRepository = $localeSettingsRepository;
        $this->local = $local;
    }

    public function execute(NewsletterCampaign $newsletterCampaign)
    {
        $result = null;

        $localeSettings = $this->localeSettingsRepository->findBy('\Core\Domain\Entity\LocaleSettings');

        $currentDateTime = new \DateTime('now', (new \DateTimezone($localeSettings[0]->getTimezone())));
        if ($newsletterCampaign->getScheduledTime() <= $currentDateTime &&
            $newsletterCampaign->getScheduledStatus() === 1) {
            $result = $this->_scheduleNewsletter($newsletterCampaign);
        }

        return $result;
    }

    private function _scheduleNewsletter($newsletterCampaign)
    {
        $newsletterCampaignScheduledTime = $newsletterCampaign->getScheduledTime();
        $newsletterCampaignScheduledTimeUTC = new \DateTime(
            $newsletterCampaignScheduledTime->format("d-m-Y\\TH:i:s"),
            (new \DateTimezone("UTC"))
        );

        $bulkEmail = new BulkEmail;
        $bulkEmail->setTimeStamp($newsletterCampaignScheduledTimeUTC->getTimestamp()*1000);
        $bulkEmail->setEmailType('newsletter');
        $bulkEmail->setLocal($this->local);
        $bulkEmail->setReferenceId($newsletterCampaign->getId());

        // Creating a new document in object store
        $this->bulkEmailRepository->save($bulkEmail);

        return $newsletterCampaign->getCampaignName() . " (" . $newsletterCampaign->getId() . ") " . strtoupper($this->local);
    }
}
