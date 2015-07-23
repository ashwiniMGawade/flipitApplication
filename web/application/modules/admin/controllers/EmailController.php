<?php

class Admin_EmailController extends Zend_Controller_Action
{
    public $flashMessenger = '';
    public $_settings = false ;
    public $_recipientMetaData = array();
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();

        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('You have no permission to access page');
            $flashMessenger->addMessage(array('error' => $message));
            $this->_redirect('/admin');
        }

        $this->_settings  = $sessionNamespace->settings['rights'] ;
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Load the data table
     * @author asharma
     */
    public function indexAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

    }

    /**
     * Get all template list from database
     * @author asharma
     * @version 1.0
     */
    public function getEmailsAction()
    {
        $params = $this->_getAllParams();
        $emailData = \KC\Repository\Emails::getAllEmailsContent($params);

        echo Zend_Json::encode($emailData);
        die();
    }

    public function editEmailsAction()
    {
        $params = $this->_getAllParams();
        $this->view->offerId = $params['id'];
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $templateId = $params['id'];
        $templateData = \KC\Repository\Emails::getTemplateContent($templateId);
        $this->view->templateId = $templateId;
        $this->view->templateData = $templateData;
    }


    public function emailHeaderFooterAction()
    {
        # sanitize data
        $data = mysql_escape_string(
            \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $this->getRequest()->getParam('data')
            )
        );
        $templateId = $this->getRequest()->getParam('templateId');
        # check tepmlete type
        switch($this->getRequest()->getParam('template')) {
            case 'email-header':
                # update headet template content
                \KC\Repository\Emails::updateHeaderContent($data, $templateId);
                break;

            case 'email-footer':
                # update footer template content
                \KC\Repository\Emails::updateFooterContent($data, $templateId);
                break;
        }
        die ;
    }



    public function saveemailcontentAction()
    {
        # sanitize data
        $val = mysql_escape_string(
            \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $this->getRequest()->getParam('val')
            )
        );
        $templateId =  $this->getRequest()->getParam('templateId');
        \KC\Repository\Emails::updateBodyContent($val, $templateId);
        die;
    }

    public function mandrillAction()
    {
        if ($this->_request->isPost()) {
            //add the flash mesage that the newsletter has been sent
            $flash = $this->_helper->getHelper('FlashMessenger');

            $isScheduled = $this->getRequest()->getParam("isScheduled", false);

            if ($isScheduled) {
                if (\KC\Repository\Signupmaxaccount::saveScheduledNewsletter($this->getRequest())) {
                    $flash->addMessage(array('success' => $this->view->translate('Newsletter has been successfully scheduled')));
                } else {
                    $flash->addMessage(array('error' => $this->view->translate('There is some problem in your data')));
                }

                $this->_helper->redirector('emailcontent', 'accountsetting', null);
            }

            if (LOCALE == '') {
                $imgLogoMail =
                "<a href=".rtrim(HTTP_PATH_FRONTEND, '/').">
                    <img src='".HTTP_PATH."public/images/HeaderMail.gif'/>
                </a>";
                $siteName = "Kortingscode.nl";
            } else {
                $imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND, '/') .">
                        <img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/>
                </a>";
                $siteName = "Flipit.com";
            }
            FrontEnd_Helper_viewHelper::exceedMemoryLimitAndExcutionTime();
            $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('10_popularShops_list');
            //key not exist in cache

            if ($voucherflag) {

                # get 10 popular vouchercodes for news letter

                $topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10) ;
                $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes, 10);

            } else {
                $topVouchercodes = \FrontEnd_Helper_viewHelper::getFromCacheByKey('10_popularShops_list');
            }

            $categoryflag =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey('10_popularCategories_list');

            //key not exist in cache

            if ($categoryflag) {

                $topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);

                FrontEnd_Helper_viewHelper::setInCache('10_popularCategories_list', $topCategories);

            } else {
                $topCategories = \FrontEnd_Helper_viewHelper::getFromCacheByKey('10_popularCategories_list');
            }


            //Start get email locale basis
            $email_data = Signupmaxaccount::getAllMaxAccounts();
            $emailFrom  = $email_data[0]['emailperlocale'];
            $emailSubject  = $email_data[0]['emailsubject'];
            $senderName  = $email_data[0]['sendername'];
            //End get email locale basis



            //call functions to set the needed data in global arrays
            $voucherCodesData = BackEnd_Helper_viewHelper::getTopVouchercodesDataMandrill($topVouchercodes);

            $this->getVouchercodesOfCategories($topCategories);
            $this->getDirectLoginLinks();
            $this->getHeaderFooterContent();


            //set the header image for mail
            $this->headerMail = array(array('name' => 'headerMail',
                                            'content' => $imgLogoMail
                                     ),
                                    array('name' => 'headerContent',
                                            'content' => $this->headerContent
                                    ),
                                    array('name' => 'footerContent',
                                            'content' => $this->footerContent
                                    ));

            //set the static content of mail so that we can change the text in PO Edit
            $this->staticContent = array(
                                    array('name' => 'websiteName',
                                            'content' => $siteName
                                    ),
                                    array('name' => 'unsubscribe',
                                            'content' => $this->view->translate('Uitschrijven')
                                    ),
                                    array('name' => 'editProfile',
                                            'content' => $this->view->translate('Wijzigen profiel')
                                    ),
                                    array('name' => 'contact',
                                            'content' => $this->view->translate('Contact')
                                    ),
                                    array('name' => 'contactLink',
                                            'content' => HTTP_PATH_FRONTEND . 'info/contact'
                                    ),
                                    array('name' => 'moreOffersLink',
                                            'content' => HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_top-20')
                                    ),
                                    array('name' => 'moreOffers',
                                            'content' => $this->view->translate('Bekijk meer van onze top aanbiedingen') . ' >'
                                    )
                            );

            //merge all the arrays into single array
            $data = array_merge(
                $voucherCodesData['dataShopName'],
                $voucherCodesData['dataOfferName'],
                $voucherCodesData['dataShopImage'],
                $voucherCodesData['expDate'],
                $this->headerMail,
                $this->dataShopNameCat,
                $this->dataOfferNameCat,
                $this->dataShopImageCat,
                $this->expDateCat,
                $this->category
            );

            //merge the permalinks array and static content array into single array
            $dataPermalink = array_merge(
                $voucherCodesData['shopPermalink'],
                $this->shopPermalinkCat,
                $this->staticContent
            );

            //initialize mandrill with the template name and other necessary options
            $mandrill = new Mandrill_Init($this->getInvokeArg('mandrillKey'));
            $template_name = $this->getInvokeArg('newsletterTemplate');
            $template_content = $data;

            $message = array(
                    'subject'    => $emailSubject ,
                    'from_email' => $emailFrom,
                    'from_name'  => $senderName,
                    'to'         => $this->to ,
                    'inline_css' => true,
                    "recipient_metadata" =>   $this->recipientMetaData ,
                    'global_merge_vars' => $dataPermalink,
                    'merge_vars' => $this->loginLinkAndData
            );

            try {

                $mandrill->messages->sendTemplate($template_name, $template_content, $message);
                $message = $this->view->translate('Newsletter has been sent successfully');

            } catch (Mandrill_Error $e) {

                //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                $message = $this->view->translate('There is some problem in your data');

            }
            //send newsletter
            $flash->addMessage(array('success' => $message));
            //redirect to account setting controller after mail sent
            $this->_helper->redirector('emailcontent', 'accountsetting', null);
        } else {
            $this->_helper->redirector('index', 'index', null);
        }
        die;
    }

    public function emailSettingsAction()
    {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->getFlashMessage();
        if ($this->getRequest()->isPost()) {
            $sendersParameters = $this->getRequest()->getParams();
            
            if ($sendersParameters['senderName']  == '' || $sendersParameters['senderEmail'] == '') {
                $this->setFlashMessage('Error in updating Email Settings.');
            } else {
                KC\Repository\Settings::updateSendersSettings('sender_email_address', $sendersParameters['senderEmail']);
                KC\Repository\Settings::updateSendersSettings('sender_name', $sendersParameters['senderName']);
                $this->setFlashMessage('Email Settings have been updated successfully');
            }
            $this->_redirect(HTTP_PATH . 'admin/email/email-settings');
        }
        
        $sendersEmailAddress = KC\Repository\Settings::getEmailSettings('sender_email_address');
        $this->view->sendersEmailAddress = $sendersEmailAddress;
        $this->view->sendersName = KC\Repository\Settings::getEmailSettings('sender_name');
    }

    public function codeAlertAction()
    {
        $this->getFlashMessage();
    }

    public function codeAlertLogListAction()
    {
        $this->getFlashMessage();
    }

    public function codeAlertSettingsAction()
    {
        $codeAlertSettings = KC\Repository\CodeAlertSettings::getCodeAlertSettings();
        $this->view->codeAlertSettings = $codeAlertSettings;
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->getFlashMessage();

        if ($this->getRequest()->isPost()) {
            $codeAlertParameters = $this->getRequest()->getParams();
            KC\Repository\CodeAlertSettings::saveCodeAlertSettings(
                $codeAlertParameters['emailSubject'],
                $codeAlertParameters['emailHeader']
            );
            $this->setFlashMessage('Code alert Settings have been updated successfully');
            $this->_redirect(HTTP_PATH . 'admin/email/code-alert-settings');
        }
    }

    public function codealertqueueAction()
    {
        $codeAlertQueueParameters = $this->getRequest()->getParams();
        $codeAlertQueueShopId = $codeAlertQueueParameters['shopId'];
        $codeAlertQueueOfferId = $codeAlertQueueParameters['offerId'];
        $codeAlertQueue = KC\Repository\CodeAlertQueue::saveCodeAlertQueue($codeAlertQueueShopId, $codeAlertQueueOfferId);
        self::updateVarnish($codeAlertQueueOfferId);
        echo $codeAlertQueue;
        die;
    }

    public function updateVarnish($id)
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishUrls = \KC\Repository\Offer::getAllUrls($id);
        $varnishRefreshTime = (array) $varnishUrls['startDate'];
        $refreshTime = FrontEnd_Helper_viewHelper::convertOfferTimeToServerTime($varnishRefreshTime['date']);
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $varnishIndex => $varnishUrl) {
                if (!is_object($varnishUrl)) {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $varnishUrl, $refreshTime);
                }
            }
        }
        $varnishObj->addUrl(HTTP_PATH_FRONTEND, $refreshTime);
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_top-20'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_categorieen'),
            $refreshTime
        );
        $varnishObj->addUrl("http://www.flipit.com", $refreshTime);
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsfeed', $refreshTime);
            $varnishObj->addUrl(HTTP_PATH_FRONTEND. 'marktplaatsmobilefeed', $refreshTime);
        }
        
        # add url for  end date
        $varnishRefreshTime = (array) $varnishUrls['endDate'];
        $refreshTime = FrontEnd_Helper_viewHelper::convertOfferTimeToServerTime($varnishRefreshTime['date']);
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $varnishIndex => $varnishUrl) {
                if (!is_object($varnishUrl)) {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $varnishUrl, $refreshTime);
                }
            }
        }
        $varnishObj->addUrl(HTTP_PATH_FRONTEND, $refreshTime);
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_top-20'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_categorieen'),
            $refreshTime
        );
        $varnishObj->addUrl("http://www.flipit.com", $refreshTime);
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsfeed', $refreshTime);
            $varnishObj->addUrl(HTTP_PATH_FRONTEND. 'marktplaatsmobilefeed', $refreshTime);
        }
    }
    
    public function savecodealertsettingsAction()
    {
        KC\Repository\CodeAlertSettings::saveCodeAlertSettings($this->getRequest()->getParams());
        die;
    }
    
    public function savecodealertemailsubjectAction()
    {
        die;
    }

    public function savecodealertemailheaderAction()
    {
        KC\Repository\CodeAlertSettings::saveCodeAlertEmailHeader($this->getRequest()->getParams());
        die;
    }

    public function totalRecepientsAction()
    {
        if ($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');
        }

        $visitors = KC\Repository\CodeAlertQueue::getRecepientsCount();
        echo $this->_helper->json(array('recepients' => $visitors), true);
    }
    
    public function codealertlistAction()
    {
        $params = $this->_getAllParams();
        $codeAlertQueue = KC\Repository\CodeAlertQueue::getCodeAlertList($params);
        echo \Zend_Json::encode($codeAlertQueue);
        die();
    }

    public function movecodealerttotrashAction()
    {
        $codeAlert = KC\Repository\CodeAlertQueue::moveCodeAlertToTrash($this->_getParam('id'));
        if (intval($codeAlert) > 0) {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Code alert has been moved to trash');
            $flash->addMessage(array('success' => $message));
        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
        echo \Zend_Json::encode($codeAlert);
        die();
    }

    public function getFlashMessage()
    {
        $message = $this->flashMessenger->getMessages();
        $this->view->successMessage = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->errorMessage = isset($message[0]['error']) ? $message[0]['error'] : '';
        return $this;
    }

    public function setFlashMessage($messageText)
    {
        $message = $this->view->translate($messageText);
        $this->flashMessenger->addMessage(array('success' => $message));
        return $this;
    }

    public function codealertsentlistAction()
    {
        $params = $this->_getAllParams();
        $codeAlertQueue = KC\Repository\CodeAlertQueue::getCodeAlertList($params, true);
        echo \Zend_Json::encode($codeAlertQueue);
        die();
    }
}
