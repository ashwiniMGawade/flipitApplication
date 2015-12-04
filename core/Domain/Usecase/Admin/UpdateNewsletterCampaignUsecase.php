<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\NewsletterCampaignValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateNewsletterCampaignUsecase
{
    protected $newsletterCampaignRepository;

    protected $newsletterCampaignValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignRepositoryInterface $newsletterCampaignRepository,
        NewsletterCampaignValidator $newsletterCampaignValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository = $newsletterCampaignRepository;
        $this->newsletterCampaignValidator  = $newsletterCampaignValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(NewsletterCampaign $newsletterCampaign, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['campaignName'])) {
            $newsletterCampaign->setCampaignName($params['campaignName']);
        }
        if (isset($params['campaignSubject'])) {
            $newsletterCampaign->setCampaignSubject($params['campaignSubject']);
        }
        if (isset($params['senderName'])) {
            $newsletterCampaign->setSenderName($params['senderName']);
        }
        if (isset($params['header'])) {
            $newsletterCampaign->setHeaderBanner($params['header']);
        }
        if (isset($params['headerBanner'])) {
            $newsletterCampaign->setHeaderBanner($params['headerBanner']);
        }
        if (isset($params['headerBannerURL'])) {
            $newsletterCampaign->setHeaderBannerURL($params['headerBannerURL']);
        }
        if (isset($params['footer'])) {
            $newsletterCampaign->setHeaderBanner($params['footer']);
        }
        if (isset($params['footerBanner'])) {
            $newsletterCampaign->setFooterBanner($params['footerBanner']);
        }
        if (isset($params['footerBannerURL'])) {
            $newsletterCampaign->setFooterBannerURL($params['footerBannerURL']);
        }
        if (isset($params['offerPartOneTitle'])) {
            $newsletterCampaign->setOfferPartOneTitle($params['offerPartOneTitle']);
        }
        if (isset($params['offerPartTwoTitle'])) {
            $newsletterCampaign->setOfferPartTwoTitle($params['offerPartTwoTitle']);
        }
        if (isset($params['senderEmail'])) {
            $newsletterCampaign->setSenderEmail($params['senderEmail']);
        }
        if (isset($params['scheduledStatus'])) {
            $newsletterCampaign->setScheduledStatus($params['scheduledStatus']);
        }
        if (isset($params['scheduledTime'])) {
            $newsletterCampaign->setScheduledTime($params['scheduledTime']);
        }
        if (isset($params['newsletterSentTime'])) {
            $newsletterCampaign->setNewsletterSentTime($params['newsletterSentTime']);
        }
        if (isset($params['receipientCount'])) {
            $newsletterCampaign->setReceipientCount($params['receipientCount']);
        }
        if (isset($params['deleted'])) {
            $newsletterCampaign->setDeleted($params['deleted']);
        }
        $newsletterCampaign->setUpdatedAt(new \DateTime('now'));
        $validationResult = $this->newsletterCampaignValidator->validate($newsletterCampaign);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->newsletterCampaignRepository->save($newsletterCampaign);
    }
}
