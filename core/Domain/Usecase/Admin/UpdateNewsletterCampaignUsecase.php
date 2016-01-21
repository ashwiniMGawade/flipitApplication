<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;
use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\NewsletterCampaignValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateNewsletterCampaignUsecase
{
    protected $newsletterCampaignRepository;

    protected $newsletterCampaignOfferRepository;

    protected $newsletterCampaignValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignRepositoryInterface $newsletterCampaignRepository,
        NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository,
        NewsletterCampaignValidator $newsletterCampaignValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository = $newsletterCampaignRepository;
        $this->newsletterCampaignOfferRepository = $newsletterCampaignOfferRepository;
        $this->newsletterCampaignValidator  = $newsletterCampaignValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(NewsletterCampaign $newsletterCampaign, NewsletterCampaignOffer $newsletterCampaignOffer, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            $this->errors->setOriginalState($newsletterCampaign);
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['campaignName'])) {
            $newsletterCampaign->setCampaignName($params['campaignName']);
        }
        if (isset($params['campaignSubject'])) {
            $newsletterCampaign->setCampaignSubject(123);
        }
        if (isset($params['senderName'])) {
            $newsletterCampaign->setSenderName($params['senderName']);
        }
        if (isset($params['campaignHeader'])) {
            $newsletterCampaign->setHeader($params['campaignHeader']);
        }
        if (isset($params['headerBanner'])) {
            $newsletterCampaign->setHeaderBanner($params['headerBanner']);
        }
        if (isset($params['headerBannerURL'])) {
            $newsletterCampaign->setHeaderBannerURL($params['headerBannerURL']);
        }
        if (isset($params['campaignFooter'])) {
            $newsletterCampaign->setFooter($params['campaignFooter']);
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
            $newsletterCampaign->setScheduledStatus((int)$params['scheduledStatus']);
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
            $newsletterCampaign->setDeleted((int)$params['deleted']);
        }

        $newsletterCampaign->setUpdatedAt(new \DateTime('now'));
        $validationResult = $this->newsletterCampaignValidator->validate($newsletterCampaign);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            $this->errors->setOriginalState($newsletterCampaign);
            return $this->errors;
        }

       // $this->newsletterCampaignRepository->beginTransaction();
        $this->newsletterCampaignRepository->save($newsletterCampaign);

        if (isset($params['partOneOffers']) && !empty($params['partOneOffers'])) {
            $this->updateOffers(1, $newsletterCampaign, $newsletterCampaignOffer, $params['partOneOffers']);
        }

        if (isset($params['partTwoOffers']) && !empty($params['partTwoOffers'])) {
            $this->updateOffers(2, $newsletterCampaign, $newsletterCampaignOffer, $params['partTwoOffers']);
        }
        //$this->newsletterCampaignRepository->commitTransaction();
        return $newsletterCampaign;
    }

    private function updateOffers($section, $newsletterCampaign, $newsletterCampaignOffer, $offers)
    {
        $campaignOffers = $newsletterCampaign->getNewsletterCampaignOffers();
        $offerIds =[];
        if (!empty($campaignOffers)) {
            foreach ($campaignOffers as $offer) {
                if ($offer->getSection() == $section) {
                    $offerIds[] = $offer->getId();
                }
            }
            if (!empty($offerIds)) {
                $this->newsletterCampaignOfferRepository->deleteNewsletterCampaignOffers($offerIds);
            }
        }
        $params['newsletterCampaign'] = $newsletterCampaign;
        $params['section'] = $section;
        foreach ($offers as $index => $offer) {
            $params['offerId'] =  $offer;
            $params['position'] = $index +1;
            $result = $this->_createOffer($newsletterCampaignOffer, $params);
            if ($result instanceof Errors) {
                $result->setOriginalState($newsletterCampaign);
                return $result;
            }
        }
        return true;
    }

    private function _createOffer($newsletterCampaignOfferObject, $params)
    {
        $newsletterCampaignOffer = clone $newsletterCampaignOfferObject;
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['newsletterCampaign'])) {
            $newsletterCampaignOffer->setNewsletterCampaign($params['newsletterCampaign']);
        }
        if (isset($params['offerId'])) {
            $newsletterCampaignOffer->setOfferId($params['offerId']);
        }
        if (isset($params['position'])) {
            $newsletterCampaignOffer->setPosition($params['position']);
        }
        if (isset($params['section'])) {
            $newsletterCampaignOffer->setSection((int)$params['section']);
        }
        $newsletterCampaignOffer->setCreatedAt(new \DateTime('now'));
        $newsletterCampaignOffer->setUpdatedAt(new \DateTime('now'));

        return $this->newsletterCampaignOfferRepository->addNewsletterCampaignOffer($newsletterCampaignOffer);
    }
}
