<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignWarningsUsecase
{
    private $htmlPurifier;
    private $errors;

    public function __construct(PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->htmlPurifier      = $htmlPurifier;
        $this->errors            = $errors;
    }

    public function execute($newsletterCampaign, $newsletterCampaignOffers)
    {

    }
}
