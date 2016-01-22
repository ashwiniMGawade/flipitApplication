<?php

namespace Core\Domain\Usecase\Helpers;

use Core\Domain\Factory\AdminFactory;
use Core\Domain\Factory\SystemFactory;

trait NewsletterCampaignBuilder
{
    public function checkNewsletterForWarnings($newsletterCampaign, $returnWarningMessages = false)
    {
        $warnings = [];
        if ($newsletterCampaign->getScheduledStatus() == 1) {
            //another newsletter is scheduled within 24 hours
            $scheduledCampaignTime = $newsletterCampaign->getScheduledTime();
            $day = new \DateInterval("P1D");
            $dayTimeFrame = clone $scheduledCampaignTime;
            $dayTimeFrame->add($day);
            $scheduledCampaigns = AdminFactory::getNewsletterCampaignsByConditions()->execute(
                array(
                    array('id', '!=', $newsletterCampaign->getId()),
                    array('scheduledStatus', '=', 1),
                    array('scheduledTime', '>=', $scheduledCampaignTime->format("Y-m-d H:i")),
                    array('scheduledTime', '<=', $dayTimeFrame->format("Y-m-d H:i"))
                )
            );
            if (!empty($scheduledCampaigns)) {
                $warnings[] = 'Another newsletter is scheduled within 24 hours';
                if (!$returnWarningMessages) {
                    return true;
                }
            }
            //an offer is not going to be live when the newsletter is send. Keep the timezone in mind!
            $result = SystemFactory::getNewsletterCampaignsOffers()->execute(array('campaignId' => $newsletterCampaign->getId()));
            if ($result instanceof Errors) {
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                foreach ($result as $offerData) {
                    $startDate = $offerData['startDate'];
                    if ($newsletterCampaign->getScheduledTime() < $startDate) {
                        $warnings[] = 'Offer "'. $offerData['title'] . '" wont be live when newsletter is sent';
                        if (!$returnWarningMessages) {
                            return true;
                        }
                    }
                }
            }
        }
        if (!$returnWarningMessages) {
            return false;
        }
        return $warnings;
    }
}
