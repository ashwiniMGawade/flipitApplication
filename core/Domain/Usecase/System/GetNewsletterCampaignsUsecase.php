<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignsUsecase
{
    use \Core\Domain\Usecase\Helpers\NewsletterCampaignBuilder;

    private $newsletterCampaignRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(
        NewsletterCampaignRepositoryInterface $newsletterCampaignRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository    = $newsletterCampaignRepository;
        $this->htmlPurifier                    = $htmlPurifier;
        $this->errors                          = $errors;
    }

    public function execute($conditions = array(), $order = array(), $limit = 100, $offset = 0, $isPaginated = false, $returnWarnings = false)
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        if (false === $isPaginated) {
            $campaigns =$this->newsletterCampaignRepository->findBy('\Core\Domain\Entity\NewsletterCampaign', $conditions, $order, $limit, $offset);
        } else {
            $campaigns = $this->newsletterCampaignRepository->findAllPaginated('\Core\Domain\Entity\NewsletterCampaign', $conditions, $order, $limit, $offset);
        }
        if ($returnWarnings) {
            foreach ($campaigns['records'] as $newsletterCampaign) {
                $returnWarningMessages = false;
                $newsletterCampaign->warnings = $this->checkNewsletterForWarnings($newsletterCampaign, $returnWarningMessages);
            }
        }

        return $campaigns;
    }
}
