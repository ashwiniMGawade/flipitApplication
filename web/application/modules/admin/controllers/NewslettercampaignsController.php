<?php
use \Core\Domain\Factory\AdminFactory;
use \Core\Domain\Factory\SystemFactory;
use \Core\Service\Errors;

class Admin_NewslettercampaignsController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        $this->view->controllerName = $this->getRequest()
            ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['content']['rights'] != '1'
            && $sessionNamespace->settings['rights']['content']['rights'] != '2'
        ) {
            $this->_redirect('/admin/auth/index');
        }
    }
    public function init()
    {

    }

    public function indexAction()
    {
    }

    public function getnewslettercampaignlistAction()
    {
        $conditions = array('deleted' => 0);
        $campaignList = array();
        $order = $this->getOrderByField();
        $offset = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('iDisplayStart')));
        $limit = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('iDisplayLength')));

        $result = ( array )SystemFactory::getNewsletterCampaigns()->execute($conditions, $order, $limit, $offset, true);
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $campaignList['records'] = $this->prepareData($result['records']);
            $sEcho = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('sEcho')));
            $response = \DataTable_Helper::createResponse($sEcho, $campaignList['records'], $result['count']);
            echo Zend_Json::encode($response);
        }
        exit;
    }

    private function getOrderByField()
    {
        $sortColumns = array(
            'id',
            'campaignName',
            'campaignSubject',
            'scheduledTime',
            'scheduledStatus',
            'createdAt'
        );

        $orderByField = $sortColumns[intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('iSortCol_0')))];
        $orderByDirection = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('sSortDir_0'));
        $orderByDirection = !empty($orderByDirection) ? $orderByDirection : 'ASC';
        return null != $orderByField ? array($orderByField => $orderByDirection) : array();
    }

    private function prepareData($campaigns)
    {
        $returnData = array();
        if (!empty($campaigns)) {
            foreach ($campaigns as $campaign) {
                $returnData[] = array(
                    'id' => $campaign->getId(),
                    'campaignName' => $campaign->getCampaignName(),
                    'campaignSubject' => $campaign->getCampaignSubject(),
                    'scheduledStatus' => $campaign->getScheduledStatus(),
                    'scheduledTime' => $campaign->getScheduledTime(),
                    'warnings' => 'OK' //Needs to change when warning task is done
                );
            }
        }
        return $returnData;
    }

    public function createAction()
    {
        $this->view->newsletterCampaign = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $newsletterCampaign = AdminFactory::createNewsletterCampaign()->execute();
            $result = AdminFactory::addNewsletterCampaign()->execute($newsletterCampaign, $params);
            $this->view->newsletterCampaign = $this->getAllParams();
            if ($result instanceof Errors) {
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->refreshNewsletterCampaignPageVarnish();
                $this->setFlashMessage('success', 'News letter campaign has been added successfully');
                $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
            }
        } else {
            $sendersEmailAddress = KC\Repository\Settings::getEmailSettings('sender_email_address');
            $this->view->newsletterCampaign['senderEmail'] = $sendersEmailAddress;
            $this->view->newsletterCampaign['senderName'] = KC\Repository\Settings::getEmailSettings('sender_name');

            $campaignHeaderSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_HEADER'));
            $this->view->newsletterCampaign['campaignHeader'] = !empty($campaignHeaderSetting) ? $campaignHeaderSetting->value : '';

            $campaignFooterSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_FOOTER'));
            $this->view->newsletterCampaign['campaignFooter'] = !empty($campaignFooterSetting) ? $campaignFooterSetting->value : '';
        }

    }

    public function refreshNewsletterCampaignPageVarnish()
    {
        $varnishObject = new \KC\Repository\Varnish();
        $varnishObject->addUrl("http://www.flipit.com");
    }

    public function settingsAction()
    {
        $campaignHeaderSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_HEADER'));
        $this->view->campaign_header = !empty($campaignHeaderSetting) ? $campaignHeaderSetting->value : '';

        $campaignFooterSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_FOOTER'));
        $this->view->campaign_footer = !empty($campaignFooterSetting) ? $campaignFooterSetting->value : '';

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $isValid = true;
            $params['campaign-header'] = trim($params['campaign-header']);
            $params['campaign-footer'] = trim($params['campaign-footer']);
            if (true === empty($params['campaign-header'])) {
                $this->setFlashMessage('error', "campaign header is required");
                $isValid = false;
            }
            if (true === empty($params['campaign-footer'])) {
                $this->setFlashMessage('error', "campaign footer is required");
                $isValid = false;
            }
            if (true === $isValid) {
                $result = AdminFactory::updateSetting()->execute($campaignHeaderSetting, array('value' => $params['campaign-header']));
                if ($result instanceof Errors) {
                    $this->setFlashMessage('error', $result->getErrorsAll());
                    $isValid = false;
                }
                $result = AdminFactory::updateSetting()->execute($campaignFooterSetting, array('value' => $params['campaign-footer']));
                if ($result instanceof Errors) {
                    $this->setFlashMessage('error', $result->getErrorsAll());
                    $isValid = false;
                }
                if (true === $isValid) {
                    $this->setFlashMessage('success', 'Campaign settings has been updated successfully');
                    $this->redirect(HTTP_PATH . 'admin/newslettercampaigns/settings');
                }
            }
        }
    }
}
