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

    private function checkNewsletterForWarnings($newsletterCampaign, $returnWarnings = false)
    {
        $warnings = [];
        if ($newsletterCampaign->getScheduledStatus() == 1) {
            //another newsletter is scheduled within 24 hours
            $beginOfDay = strtotime("midnight", $newsletterCampaign->getScheduledTime());
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
            $scheduledCampaigns = AdminFactory::getNewsletterCampaignsByConditions()->execute(
                array(
                    array('id', '!=', $newsletterCampaign->getId()),
                    array('scheduledTime', '>=', $beginOfDay),
                    array('scheduledTime', '<=', $endOfDay)
                )
            );
            if (!empty($scheduledCampaigns)) {
                $warnings[] = 'Another newsletter is scheduled within 24 hours';
                if (!$returnWarnings) {
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
                    if ($newsletterCampaign->getScheduledTime() < $startDate->format('U')) {
                        $warnings[] = 'Offer "'. $offerData['title'] . '" wont be live when newsletter is sent';
                        if (!$returnWarnings) {
                            return true;
                        }
                    }
                }
            }
        }
        if (!$returnWarnings) {
            return false;
        }
        return $warnings;

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
                if (isset($params['schedule'])) {
                    $validationResults = AdminFactory::validateScheduledNewsletterCampaign()->execute($params);
                    if (isset($validationResults['error'])) {
                        $this->setFlashMessage('error', implode('.', $validationResults['error']));
                        return;
                    }
                    $userTimezone = new DateTimeZone($this->view->localeSettings['0']['timezone']);
                    $date = new DateTime($params['scheduleDate'] . $params['scheduleTime'], $userTimezone);
                    $params['scheduledStatus'] = 1;
                    $params['scheduledTime'] = $date->getTimestamp();
                }
                $newsletterCampaign = AdminFactory::addNewsletterCampaign()->execute($newsletterCampaign, $params);
                if ($newsletterCampaign instanceof Errors) {
                    $errors = $newsletterCampaign->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    if (isset($params['partOneOffers']) && !empty($params['partOneOffers'])) {
                        $this->updateOffers(1, $newsletterCampaign, $params['partOneOffers']);
                    }
                    if (isset($params['partTwoOffers']) && !empty($params['partTwoOffers'])) {
                        $this->updateOffers(2, $newsletterCampaign, $params['partTwoOffers']);
                    }
                    $this->refreshNewsletterCampaignPageVarnish();
                    $this->setFlashMessage('success', 'News letter campaign has been created successfully.</br>'. implode('<br/>', $this->message));
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
        $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        $this->view->recipientCount = SystemFactory::getNewsletterReceipientCount()->execute();
        $this->view->partOneSearchOffers = \KC\Repository\PopularCode::searchAllOffer(array());
        $this->view->partTwoSearchOffers = \KC\Repository\PopularCode::searchAllOffer(array());
    }

    public function editAction()
    {
        $parameters = $this->getAllParams();
        $this->view->newsletterCampaign = array();
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id'=>$parameters['id']));
        if ($newsletterCampaign instanceof Errors) {
            $errors = $newsletterCampaign->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
        }

        $this->_getOffersOfCampaign($parameters['id']);
        $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            if (isset($params['schedule'])) {
                $validationResults = AdminFactory::validateScheduledNewsletterCampaign()->execute($params);
                if (isset($validationResults['error'])) {
                    $this->setFlashMessage('error', implode('.', $validationResults['error']));
                    return;
                }
                $userTimezone = new DateTimeZone($this->view->localeSettings['0']['timezone']);
                $date = new DateTime($params['scheduleDate'] . $params['scheduleTime'], $userTimezone);
                $params['scheduledStatus'] = 1;
                $params['scheduledTime'] = $date->getTimestamp();
            }
            $params = $this->_handleImageUpload($params, $newsletterCampaign->headerBanner, $newsletterCampaign->footerBanner);
            $this->view->newsletterCampaign = $this->getAllParams();
            if ($params) {
                $newsletterCampaign = AdminFactory::updateNewsletterCampaign()->execute($newsletterCampaign, $params);
                if ($newsletterCampaign instanceof Errors) {
                    $errors = $newsletterCampaign->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    if (isset($params['partOneOffers']) && !empty($params['partOneOffers'])) {
                        $this->updateOffers(1, $newsletterCampaign, $params['partOneOffers']);
                    }
                    if (isset($params['partTwoOffers']) && !empty($params['partTwoOffers'])) {
                        $this->updateOffers(2, $newsletterCampaign, $params['partTwoOffers']);
                    }
                    $this->refreshNewsletterCampaignPageVarnish();
                    $this->setFlashMessage('success', 'News letter campaign has been updated successfully.</br>'. implode('<br/>', $this->message));
                    $this->redirect(HTTP_PATH . 'admin/newslettercampaigns');
                }
            }
        }
        if ($newsletterCampaign instanceof Errors) {
            $errors = $newsletterCampaign->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $this->view->newsletterCampaign = $this->_dismount($newsletterCampaign);
        }
        $this->view->warnings = $this->checkNewsletterForWarnings($newsletterCampaign, true);
        $this->view->recipientCount = SystemFactory::getNewsletterReceipientCount()->execute();
    }

    private function updateOffers($section, $newsletterCampaign, $offers)
    {
        $result = SystemFactory::getNewsletterCampaignsOffers()->execute(array('section' => $section, 'campaignId' => $newsletterCampaign->getId()));

        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $offerIds =[];
            if (!empty($result)) {
                foreach ($result as $index => $offer) {
                    $offerIds[] = $offer[0]['id'];
                }
                AdminFactory::deleteNewsletterCampaignOffer()->execute($offerIds);
            }
            $params['newsletterCampaign'] = $newsletterCampaign;
            if ($params['newsletterCampaign'] instanceof Errors) {
                $errors = $params['newsletterCampaign']->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            }
            $params['section'] = $section;

            foreach ($offers as $index => $offer) {
                $params['offer'] = SystemFactory::getOffer()->execute(array('id' => $offer));
                if ($params['offer'] instanceof Errors) {
                    $errors = $params['offer']->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                }
                $params['position'] = $index +1;
                $this->_createOffer($params);
            }
        }
    }

    private function _createOffer($params)
    {
        $campaignOffer = AdminFactory::createNewsletterCampaignOffer()->execute();
        $result = AdminFactory::addNewsletterCampaignOffer()->execute($campaignOffer, $params);
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
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
        $campaignHeaderSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_HEADER'));
        $this->view->campaign_header = !empty($campaignHeaderSetting) ? $campaignHeaderSetting->value : '';

        $campaignFooterSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_FOOTER'));
        $this->view->campaign_footer = !empty($campaignFooterSetting) ? $campaignFooterSetting->value : '';
    }

    private function prepareData($campaigns)
    {
        $returnData = array();
        $localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        if (!empty($campaigns)) {
            foreach ($campaigns as $campaign) {
                $scheduledTime = $campaign->getScheduledTime();
                $scheduledDate = '';
                if (!empty($scheduledTime)) {
                    $userTimezone = new DateTimeZone($localeSettings['0']['timezone']);
                    $date = DateTime::createFromFormat('U', $scheduledTime);
                    $date->setTimeZone($userTimezone);
                    $scheduledDate =  $date->format('Y-m-d H:i:s');
                }
                $warnings = $this->checkNewsletterForWarnings($campaign);

                $returnData[] = array(
                    'id' => $campaign->getId(),
                    'campaignName' => $campaign->getCampaignName(),
                    'campaignSubject' => $campaign->getCampaignSubject(),
                    'scheduledStatus' => $campaign->getScheduledStatus(),
                    'scheduledTime' => $scheduledDate,
                    'warnings' => (!$warnings) ? 'OK' : 'Warnings',
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

    private function _getOffersOfCampaign($campaignId)
    {
        $conditions = array('deleted' => 0);

        $existingPartOneOffers = [];
        $existingPartTwoOffers = [];

        if (! empty($campaignId)) {
            $conditions['campaignId'] = $campaignId;
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
        $campaignOffers = SystemFactory::getNewsletterCampaignsOffers()->execute($conditions, null, null, null, true);
        if ($campaignOffers instanceof Errors) {
            $errors = $campaignOffers->getErrorsAll();
            $this->setFlashMessage('error', $errors);
        } else {
            $campaignOffersData = [];
            if (false == empty($campaignOffers)) {
                foreach ($campaignOffers as $campaignOffer) {
                    $campaignOffersData[] = array(
                        'id' => $campaignOffer['id'],
                        'offer' => $campaignOffer['title'],
                        'position' => $campaignOffer[0]['position'],
                        'shop' => ''
                    );
                }
            }
            return $campaignOffersData;
        }
        return array();
    }
}
