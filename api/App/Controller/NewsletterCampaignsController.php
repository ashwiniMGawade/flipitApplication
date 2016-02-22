<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class NewsletterCampaignsController extends ApiBaseController
{
    public function getNewsletterCampaigns()
    {
        $page = (int) $this->app->request()->get('page');
        $perPage = (int) $this->app->request()->get('perPage');
        $perPage = ($perPage === 0) ? 100 : $perPage;
        $conditions = array();
        $currentLink = '/newsletterCampaigns?page=' . ($page) . '&perPage=' . $perPage;
        $nextLink = '/newsletterCampaigns?page=' . ($page + 1) . '&perPage=' . $perPage;

        $currentLink .= '&api_key='.urlencode($this->app->request()->get('api_key'));
        $nextLink .= '&api_key='.urlencode($this->app->request()->get('api_key'));
        $newsletterCampaigns = SystemFactory::getNewsletterCampaigns()->execute($conditions, array(), $perPage, $page);
        if ($newsletterCampaigns instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaigns->getErrorsAll())));
        }
        $newsletterCampaignsData = new Hal($currentLink);
        //ToDo : Write a limit logic
        if (count($newsletterCampaigns) === $perPage) {
            $newsletterCampaignsData->addLink('next', $nextLink);
        }

        foreach ($newsletterCampaigns as $newsletterCampaign) {
            $newsletterCampaign =  $this->generateNewsletterCampaignJsonData($newsletterCampaign);
            $newsletterCampaignsData->addResource('newsletterCampaigns', $newsletterCampaign);
        }
        echo $newsletterCampaignsData->asJson();
    }

    public function getNewsletterCampaign($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $conditions = array('id' => $id);
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute($conditions);
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaign->getErrorsAll())));
        }
        $newsletterCampaign = $this->generateNewsletterCampaignJsonData($newsletterCampaign);
        echo $newsletterCampaign->asJson();
    }

    public function updateNewsletterCampaign($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $params = json_decode($this->app->request->getBody(), true);
        $conditions = array('id' => $id);
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute($conditions);
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaign->getErrorsAll())));
        }
        $params = $this->formatInput($params);
        $campaignOffer = AdminFactory::createNewsletterCampaignOffer()->execute();
        $result = AdminFactory::updateNewsletterCampaign()->execute($newsletterCampaign, $campaignOffer, $params);
        if ($result instanceof Errors) {
            $this->app->halt(405, json_encode(array('messages' => $result->getErrorsAll())));
        }

        $response = $this->generateNewsletterCampaignJsonData($result);
        echo $response->asJson();
    }

    private function formatInput($params)
    {
        if (isset($params['scheduledStatus'])) {
            $params['scheduledStatus'] = ( $params['scheduledStatus'] === 'Pending' ? 0 : ( $params['scheduledStatus'] === 'Scheduled' ? 1 : ( $params['scheduledStatus'] === 'Triggered' ? 2 : ( $params['scheduledStatus'] === 'Sent' ? 3 : null ))));
            if (true === is_null($params['scheduledStatus'])) {
                unset($params['scheduledStatus']);
            }
        }
        if (isset($params['deleted'])) {
            $params['deleted'] = ( $params['deleted'] === 'Yes' ? 1 : ( $params['deleted'] === 'No' ? 0 : null ));
            if (true === is_null($params['deleted'])) {
                unset($params['deleted']);
            }
        }
        return $params;
    }

    private function generateNewsletterCampaignJsonData($newsletterCampaign)
    {
        $newsletterSentTime = $newsletterCampaign->getNewsletterSentTime();
        $scheduledTime = $newsletterCampaign->getScheduledTime();

        $newsletterCampaignData = array(
            'id' => $newsletterCampaign->getId(),
            'campaignName' => $newsletterCampaign->getCampaignName(),
            'campaignSubject' => $newsletterCampaign->getCampaignSubject(),
            'senderName' => $newsletterCampaign->getSenderName(),
            'senderEmail' => $newsletterCampaign->getSenderEmail(),
            'header' => $newsletterCampaign->getHeader(),
            'headerBannerURL' => $newsletterCampaign->getHeaderBannerURL(),
            'footer' => $newsletterCampaign->getFooter(),
            'footerBannerURL' => $newsletterCampaign->getFooterBannerURL(),
            'offerPartOneTitle' => $newsletterCampaign->getOfferPartOneTitle(),
            'offerPartTwoTitle' => $newsletterCampaign->getOfferPartTwoTitle(),
            'scheduledStatus' => (0 === $newsletterCampaign->getScheduledStatus() ? 'Pending' : ( 1 === $newsletterCampaign->getScheduledStatus() ? 'Scheduled' : ( 2 === $newsletterCampaign->getScheduledStatus() ? 'Triggered' : 'Sent' ))),
            'scheduledTime' => !empty($scheduledTime) ? $scheduledTime->format('Y-m-d H:i:s') : '',
            'newsletterSentTime' => !empty($newsletterSentTime) ? $newsletterSentTime->format('Y-m-d H:i:s') : '',
            'recipientCount' => $newsletterCampaign->getReceipientCount(),
            'deleted' => (1 === $newsletterCampaign->getDeleted()) ? 'Yes' : 'No'
        );
        return new Hal('/newsletterCampaigns/'.$newsletterCampaign->getId(), $newsletterCampaignData);
    }
}
