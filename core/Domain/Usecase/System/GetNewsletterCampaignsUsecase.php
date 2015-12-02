<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignsUsecase
{
    private $newsletterCampaignRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(NewsletterCampaignRepositoryInterface $newsletterCampaignRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->newsletterCampaignRepository    = $newsletterCampaignRepository;
        $this->htmlPurifier                    = $htmlPurifier;
        $this->errors                          = $errors;
    }

    public function execute($conditions = array(), $order = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->newsletterCampaignRepository->findBy('\Core\Domain\Entity\NewsletterCampaign', $conditions, $order);
    }
}
