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


    public function createAction()
    {
        $this->view->newsletterCampaign = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $newsletterCampaign = AdminFactory::createNewsletterCampaign()->execute();
            $params = $this->_handleImageUpload($params);
            $this->view->newsletterCampaign = $this->getAllParams();
            if ($params) {
                $result = AdminFactory::addNewsletterCampaign()->execute($newsletterCampaign, $params);
                if ($result instanceof Errors) {
                    $errors = $result->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    $this->refreshNewsletterCampaignPageVarnish();
                    $this->setFlashMessage('success', 'News letter campaign has been added successfully.</br>'. implode('<br/>', $this->message));
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
        }
    }

    public function editAction()
    {
        $parameters = $this->getAllParams();
        $this->view->campaignID = $parameters['id'];
        $this->view->newsletterCampaign = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id'=>$this->view->campaignID));
            $params = $this->_handleImageUpload($params, $newsletterCampaign->headerBanner, $newsletterCampaign->footerBanner);
            $this->view->newsletterCampaign = $this->getAllParams();
            if ($params) {
                $result = AdminFactory::addNewsletterCampaign()->execute($newsletterCampaign, $params);
                if ($result instanceof Errors) {
                    $errors = $result->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    $this->refreshNewsletterCampaignPageVarnish();
                    $this->setFlashMessage('success', 'News letter campaign has been updated successfully.</br>'. implode('<br/>', $this->message));
                    $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
                }
            }
        }

        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id'=>$this->view->campaignID));
        if ($newsletterCampaign instanceof Errors) {
            $errors = $newsletterCampaign->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $this->view->newsletterCampaign = $this->_dismount($newsletterCampaign);
        }
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

    private function _dismount($object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
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

    private function _handleImageUpload($params, $headerBanner = '', $footerBanner = '')
    {
        if (true === isset($_FILES['headerBanner']) && true === isset($_FILES['headerBanner']['name']) && '' !== $_FILES['headerBanner']['name']) {
            $rootPath = BASE_PATH . 'images/upload/newslettercampaigns/';
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
            $rootPath = BASE_PATH . 'images/upload/newslettercampaigns/';
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

    public function uploadImage($file, $rootPath)
    {
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0755, true);
        } elseif (!is_writable($rootPath)) {
            chmod($rootPath, 0755);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png,JPG,PNG', true));
        $imageName = pathinfo($adapter->getFileName($file, false));
        $imageName = isset($imageName['extension']) ? time().'.'.$imageName['extension'] : '';
        $targetPath = $rootPath . $imageName;
        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array('target' => $targetPath, 'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return $imageName;
        } else {
            return false;
        }
    }

    public function refreshNewsletterCampaignPageVarnish()
    {
        $varnishObject = new \KC\Repository\Varnish();
        $varnishObject->addUrl("http://www.flipit.com");
    }

    public function getofferslistAction()
    {
        $this->_helper-> layout()->disableLayout();
        $conditions = array('deleted' => 0);
        $params = $this->getRequest()->getParams();
        $campaignId = isset($params['campaignId']) ? $params['campaignId']: 1;
        if (! empty($campaignId)) {
            $campaignDetails = AdminFactory::getNewsletterCampaign()->execute(array('id' => $campaignId));
            if ($campaignDetails instanceof Errors) {
                $errors = $campaignDetails->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $conditions['campaignId'] = $campaignId;
            }
        }
        $this->view->partOneOffers = $this->_getSectionOffers($conditions, 1);
        $this->view->partTwoOffers = $this->_getSectionOffers($conditions, 2);

        foreach ($this->view->partOneOffers as $pOffer) {
            $existingPartOneOffers[] = $pOffer['id'];
        }

        foreach ($this->view->partTwoOffers as $pOffer) {
            $existingPartTwoOffers[] = $pOffer['id'];
        }

        $this->view->partOneSearchOffers = \KC\Repository\PopularCode::searchAllOffer($existingPartOneOffers);
        $this->view->partTwoSearchOffers = \KC\Repository\PopularCode::searchAllOffer($existingPartTwoOffers);
     
    }

    private function _getSectionOffers($conditions, $section)
    {
        $conditions['section'] = $section;
        $result = SystemFactory::getNewsletterCampaignsOffers()->execute($conditions, null, null, null, true);
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $campaignOffersData = [];
            $campaignOffers = $result['records'];
            if (false == empty($campaignOffers)) {
                foreach ($campaignOffers as $campaignOffer) {
                    $offer = SystemFactory::getOffer()->execute(array( 'id' => $campaignOffer->getOfferId()));
                    if ($offer instanceof  \Core\Domain\Entity\Offer) {
                        $campaignOffersData[] = array(
                            'id' => $campaignOffer->getId(),
                            'title' => $campaignOffer->getOffer()->getTitle(),
                            'position' => $campaignOffer->getPosition(),
                        );
                    }
                }
            }
            return $campaignOffersData;
        }
        return array();
    }
}
