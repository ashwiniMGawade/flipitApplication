<?php

namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\BulkEmailRepositoryInterface;
use Core\Domain\Entity\BulkEmail;

class SendTestNewsletterUsecase
{
    private $bulkEmailRepository;

    public function __construct(
        BulkEmailRepositoryInterface $bulkEmailRepository
    ) {
        $this->bulkEmailRepository = $bulkEmailRepository;
    }

    public function execute($newsletterCampaignId, $intVisitorId, $local)
    {
        $bulkEmail = new BulkEmail;
        $bulkEmail->setTimeStamp(time()*1000);
        $bulkEmail->setEmailType('testnewsletter');
        $bulkEmail->setLocal($local);
        $bulkEmail->setReferenceId($newsletterCampaignId);
        $bulkEmail->setUserId($intVisitorId);

        return $this->bulkEmailRepository->save($bulkEmail);
    }
}
