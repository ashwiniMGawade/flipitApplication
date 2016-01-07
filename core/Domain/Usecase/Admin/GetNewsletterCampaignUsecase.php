<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Domain\Repository\LocalSettingsRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignUsecase
{
    use \Core\Domain\Usecase\Helpers\NewsletterCampaignBuilder;

    protected $newsletterCampaignRepository;

    protected $localSettingsRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignRepositoryInterface $newsletterCampaignRepository,
        LocalSettingsRepositoryInterface $localSettingsRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository = $newsletterCampaignRepository;
        $this->localSettingsRepository = $localSettingsRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
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

        $localeSettings =  $this->localSettingsRepository->findBy('\Core\Domain\Entity\LocaleSettings');
        $newsletterCampaign->warnings = $this->checkNewsletterForWarnings($newsletterCampaign, $localeSettings);

        return $newsletterCampaign;
    }
}
