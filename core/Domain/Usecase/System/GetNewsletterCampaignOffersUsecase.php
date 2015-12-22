<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignOffersUsecase
{
    private $newsletterCampaignOfferRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->newsletterCampaignOfferRepository    = $newsletterCampaignOfferRepository;
        $this->htmlPurifier                         = $htmlPurifier;
        $this->errors                               = $errors;
    }

    public function execute($conditions = array(), $order = array(), $limit = 100, $offset = 0, $isPaginated = false)
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        if (false === $isPaginated) {
            $campaigns =$this->newsletterCampaignOfferRepository->findBy('\Core\Domain\Entity\NewsletterCampaignOffer', $conditions, $order, $limit, $offset);
        } else {
            $campaigns = $this->newsletterCampaignOfferRepository->findAllPaginated('\Core\Domain\Entity\NewsletterCampaignOffer', $conditions, $order, $limit, $offset);
        }

        return $campaigns;
    }
}
