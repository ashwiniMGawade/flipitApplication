<?php
use \Core\Domain\Factory\AdminFactory;
use \Core\Domain\Factory\SystemFactory;
use \Core\Service\Errors;

class Admin_NewslettercampaignsController extends Application_Admin_BaseController
{
    protected $message = [];
    public function preDispatch()
    {
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()->getParam('controller');
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

        $getPaginatedResults = true;
        $getCampaignWarnings = true;

        $result = (array) SystemFactory::getNewsletterCampaigns()->execute($conditions, $order, $limit, $offset, $getPaginatedResults, $getCampaignWarnings);

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

    private function _assignScheduleTimeSettings($params)
    {
        if (isset($params['schedule'])) {
            $validationResults = AdminFactory::validateScheduledNewsletterCampaign()->execute($params);
            if (isset($validationResults['error'])) {
                $msg = '';
                foreach ($validationResults['error'] as $element => $error) {
                    $msg .= "<div for=".$element." class='error help-inline'>".$error."</div>";
                }
                $this->setFlashMessage('error', $msg);
                return;
            }
            $userTimezone = new DateTimeZone($this->view->localeSettings['0']['timezone']);
            $date = new DateTime($params['scheduleDate'] . $params['scheduleTime'], $userTimezone);
            $params['scheduledStatus'] = 1;
            $params['scheduledTime'] = $date;
        }
        return $params;
    }

    public function createAction()
    {
        $this->view->newsletterCampaign = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $newsletterCampaign = AdminFactory::createNewsletterCampaign()->execute();
            $params = $this->_handleImageUpload($params);
            $this->view->newsletterCampaign = $this->getAllParams();
            $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
            if ($params) {
                $params = $this->_assignScheduleTimeSettings($params);
                $campaignOffer = AdminFactory::createNewsletterCampaignOffer()->execute();
                $newsletterCampaign = AdminFactory::addNewsletterCampaign()->execute($newsletterCampaign, $campaignOffer, $params);
                if (!is_object($newsletterCampaign) && isset($newsletterCampaign['error'])) {
                    $errors = $newsletterCampaign['error']->getErrorsAll();
                    $newsletterCampaign = $newsletterCampaign['newsletterCampaign'];
                    $this->setFlashMessage('error', $errors);
                } else {
                    $this->setFlashMessage('success', 'Newsletter campaign has been created successfully.</br>'. implode('<br/>', $this->message));
                    $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
                }
            }
        } else {
            $sendersEmailAddress = KC\Repository\Settings::getEmailSettings('sender_email_address');
            $this->view->newsletterCampaign['senderEmail'] = $sendersEmailAddress;
            $this->view->newsletterCampaign['senderName'] = KC\Repository\Settings::getEmailSettings('sender_name');

            $campaignHeaderSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_HEADER'));
            $this->view->newsletterCampaign['campaignHeader'] = !empty($campaignHeaderSetting) ? $campaignHeaderSetting->value : '';

            $campaignFooterSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_FOOTER'));
            $this->view->newsletterCampaign['campaignFooter'] = !empty($campaignFooterSetting) ? $campaignFooterSetting->value : '';

            $this->view->recipientCount = SystemFactory::getNewsletterReceipientCount()->execute();
            $this->view->partTwoSearchOffers = $this->view->partOneSearchOffers = \KC\Repository\PopularCode::searchAllOffer(array());
        }
    }

    public function editAction()
    {
        $parameters = $this->getAllParams();
        $this->view->newsletterCampaign = array();
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id'=>$parameters['id']), true);
        if ($newsletterCampaign instanceof Errors) {
            $errors = $newsletterCampaign->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
        }
        $this->view->newsletterCampaign =$newsletterCampaign;
        $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $params = $this->_assignScheduleTimeSettings($params);
            $params = $this->_handleImageUpload($params, $newsletterCampaign->headerBanner, $newsletterCampaign->footerBanner);
            if ($params) {
                $campaignOffer = AdminFactory::createNewsletterCampaignOffer()->execute();
                $newsletterCampaign = AdminFactory::updateNewsletterCampaign()->execute($newsletterCampaign, $campaignOffer, $params);
                if (!is_object($newsletterCampaign) && isset($newsletterCampaign['error'])) {
                    $errors = $newsletterCampaign['error']->getErrorsAll();
                    $newsletterCampaign = $newsletterCampaign['newsletterCampaign'];
                    $this->view->newsletterCampaign = $newsletterCampaign;
                    $this->setFlashMessage('error', $errors);
                } else {
                    $this->setFlashMessage('success', 'News letter campaign has been updated successfully.</br>'. implode('<br/>', $this->message));
                    $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
                }
            }
        } else {
            $this->_getSearchOffers($newsletterCampaign->getNewsletterCampaignOffers());
            $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        }
        $this->view->warnings = $newsletterCampaign->warnings;
        $this->view->recipientCount = SystemFactory::getNewsletterReceipientCount()->execute();
    }

    public function deleteAction()
    {
        $newsletterCampaignId = intval($this->getRequest()->getParam('id'));
        if (intval($newsletterCampaignId) < 1) {
            $this->setFlashMessage('error', 'Invalid selection.');
            $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
        }

        $result = AdminFactory::getNewsletterCampaign()->execute(array('id' => $newsletterCampaignId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
        }
        AdminFactory::deleteNewsletterCampaign()->execute($result);
        $this->setFlashMessage('success', 'Newsletter campaign successfully deleted.');
        $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
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
                    'warnings' => (!$campaign->warnings) ? 'OK' : 'Warnings',
                );
            }
        }
        return $returnData;
    }

    private function _handleImageUpload($params, $headerBanner = '', $footerBanner = '')
    {
        if (true === isset($_FILES['headerBanner']) && true === isset($_FILES['headerBanner']['name']) && '' !== $_FILES['headerBanner']['name']) {
            $rootPath = UPLOAD_IMG_PATH . 'newslettercampaigns/';
            $image = $this->uploadImage('headerBanner', $rootPath);
            if (false === $image) {
                $this->setFlashMessage('error', "Please upload valid header banner.");
                return false;
            }
            if (false !== $image && !empty($headerBanner)) {
                @unlink(BASE_PATH . 'images/upload/newslettercampaigns/'.$headerBanner);
            }
            $this->message[] = "Successfully uploaded header banner image.";
            $params['headerBanner'] = $image;
        }
        if (true === isset($_FILES['footerBanner']) && true === isset($_FILES['footerBanner']['name']) && '' !== $_FILES['footerBanner']['name']) {
            $rootPath = UPLOAD_IMG_PATH . 'newslettercampaigns/';
            $image = $this->uploadImage('footerBanner', $rootPath);
            if (false === $image) {
                $this->setFlashMessage('error', "please upload valid footer banner.");
                return false;
            }
            if (false !== $image && !empty($footerBanner)) {
                @unlink(BASE_PATH . 'images/upload/newslettercampaigns/'.$footerBanner);
            }
            $this->message[] = "Successfully uploaded footer banner image.";
            $params['footerBanner'] = $image;
        }
        return $params;
    }

    private function _getSearchOffers($campaignOffers)
    {
        $existingPartOneOffers = $existingPartTwoOffers = array();
        foreach ($campaignOffers as $offer) {
            if ($offer->getSection() == 1) {
                $existingPartOneOffers[] = $offer->offer->getId();
            }
            if ($offer->getSection() == 2) {
                $existingPartTwoOffers[] = $offer->offer->getId();
            }
        }
        $this->view->partOneSearchOffers = \KC\Repository\PopularCode::searchAllOffer($existingPartOneOffers);
        $this->view->partTwoSearchOffers = \KC\Repository\PopularCode::searchAllOffer($existingPartTwoOffers);
    }
}
