<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class DeleteNewsletterCampaignOfferUsecase
{
    private $newsletterCampaignOfferRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignOfferRepository = $newsletterCampaignOfferRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($offerIds)
    {
        if (empty($offerIds)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $offerIds = $this->htmlPurifier->purify($offerIds);

        return $this->newsletterCampaignOfferRepository->deleteNewsletterCampaignOffers($offerIds);
    }
}
