<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignsByConditionsUsecase
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

    public function execute($conditions = array(), $order = array(), $limit = 100, $offset = 0)
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
       // $conditions = $this->htmlPurifier->purify($conditions);
        $campaigns =$this->newsletterCampaignRepository->findByConditions('\Core\Domain\Entity\NewsletterCampaign', $conditions, $order, $limit, $offset);

        return $campaigns;
    }
}
