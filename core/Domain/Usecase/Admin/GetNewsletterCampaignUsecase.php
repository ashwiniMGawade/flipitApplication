<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignUsecase
{
    use \Core\Domain\Usecase\Helpers\NewsletterCampaignBuilder;

    protected $newsletterCampaignRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignRepositoryInterface $newsletterCampaignRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository = $newsletterCampaignRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions, $returnWarnings = false)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find campaign.');
            return $this->errors;
        }

        $newsletterCampaign = $this->newsletterCampaignRepository->findOneBy('\Core\Domain\Entity\NewsletterCampaign', $conditions);

        if (false === is_object($newsletterCampaign)) {
            $this->errors->setError('Newsletter Campaign not found');
            return $this->errors;
        }

        if ($returnWarnings) {
            $returnWarningMessages = true;
            $newsletterCampaign->warnings = $this->checkNewsletterForWarnings($newsletterCampaign, $returnWarningMessages);
        }

        return $newsletterCampaign;
    }
}
