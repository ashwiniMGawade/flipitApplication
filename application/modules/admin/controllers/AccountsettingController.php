<?php
/**
 * all the regarding Email Lightbox functionality
 * @author sunny patial
 *
 */
class Admin_AccountsettingController extends Zend_Controller_Action
{
    public $dataShopName = array();
    public $dataOfferName = array();
    public $dataShopImage = array();
    public $expDate = array();
    public $shopPermalink = array();

    public $category = array();
    public $dataShopNameCat = array();
    public $dataOfferNameCat = array();
    public $dataShopImageCat = array();
    public $expDateCat = array();
    public $shopPermalinkCat = array();
    public $_recipientMetaData  = array();

    public $headerMail = array();
    public $_loginLinkAndData = array();
    public $_to = array();
    public $staticContent = array();
    public $headerContent = array();
    public $footerContent = array();

    # holds settings regarding user rights
    public $_settings = false ;
    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author sunny patial
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'] ;


        if(! $this->getRequest()->isXmlHttpRequest()) {

            # add action as new case which needs to be viewed by other users
            switch(strtolower($this->view->action)) {
                case 'emailcontent':
                case 'mandrill':
                break;
                default:
                    if( $this->_settings['system manager']['rights'] != '1' ) {
                        $this->_redirect('/admin/auth/index');
                    }

            }

        } else {

            # add action as new case which needs to be viewed by other users
            switch(strtolower($this->view->action)) {
                case 'madrill':
                case 'changemailconfirmation':
                case 'saveemailcontent' :
                case 'email-header-footer' :
                break;
                default:
                    if( $this->_settings['system manager']['rights'] != '1' ) {
                        $this->getResponse()->setHttpResponseCode(404);
                        $this->_helper->redirector('index' , 'index' , null ) ;
                    }

            }

        }

    }
    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
        /* Initialize action controller here */
    }

    /**
     * get stores to step 2 create account from database
     * get Codes for No more free logins from database
     * @author sunny patial
     * @version 1.0
     */
    public function indexAction()
    {
        // action body
        $store_data = Signupfavoriteshop::getalladdstore();
        $this->view->store_data = $store_data;
        $data = Signupcodes::getfreeCodelogin();
        $this->view->codelogindata = $data;
        $maxacc_data = Signupmaxaccount::getAllMaxAccounts();
        $this->view->maxacc_data = $maxacc_data;
    }



    /**
     * Change email confimation status on click on yes no button
     */
    public function changemailconfirmationAction()
    {
        $status=$this->getRequest()->getParam("status");
        Signupmaxaccount::changeEmailConfimationSetting($status);
        die;
    }

    public function mandrillAction()
    {
        if ($this->_request->isPost()) {
            $flashMessage = $this->_helper->getHelper('FlashMessenger');
            $isScheduled = $this->getRequest()->getParam("isScheduled", false);

            if ($isScheduled) {
                $messageStatusResult = Signupmaxaccount::saveScheduledNewsletter($this->getRequest());
                switch ($messageStatusResult) {
                    case '1':
                        NewsLetterCache::saveNewsLetterCacheContent();
                            $flashMessage->addMessage(
                                array(
                                    'success' => $this->view->translate('Newsletter has been successfully scheduled')
                                )
                            );
                        break;
                    case '2':
                        $flashMessage->addMessage(
                            array(
                                'error' => $this->view->translate('You have already scheduled the Newsletter for the same day.')
                            )
                        );
                        break;
                    case '3':
                        $flashMessage->addMessage(
                            array(
                                'error' => $this->view->translate('You cannot schedule a newsletter in the past.')
                            )
                        );
                        break;
                    default:
                        break;
                }
                $this->_helper->redirector('emailcontent', 'accountsetting', null);
            }

            FrontEnd_Helper_viewHelper::exceedMemoryLimitAndExcutionTime();
            $topVouchercodes = Offer::getTopOffers(10);
            $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('10_popularCategories_list');
            if ($categoryflag) {
                $topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);
                FrontEnd_Helper_viewHelper::setInCache('10_popularCategories_list', $topCategories);
            } else {
                $topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('10_popularCategories_list');
            }

            $emailDetails = Signupmaxaccount::getAllMaxAccounts();
            $mandrillSenderEmailAddress = $emailDetails[0]['emailperlocale'];
            $mandrillNewsletterSubject = $emailDetails[0]['emailsubject'];
            $mandrillSenderName = $emailDetails[0]['sendername'];
            BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this);
            BackEnd_Helper_MandrillHelper::getHeaderFooterContent($this);
            $mandrill = new Mandrill_Init($this->getInvokeArg('mandrillKey'));
            $categoryVouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']), 0, 3);
            $categoryName = $topCategories[0]['category']['name'];
            $categoryPermalink = $topCategories[0]['category']['permaLink'];
            $newsletterHeader = Signupmaxaccount::getEmailHeaderFooter();
            try {
                FrontEnd_Helper_viewHelper::sendMandrillNewsletterByBatch(
                    $topVouchercodes,
                    $categoryVouchers,
                    $categoryName.'|'.$categoryPermalink,
                    $mandrillNewsletterSubject,
                    $mandrillSenderEmailAddress,
                    $mandrillSenderName,
                    $this->_recipientMetaData,
                    $this->_loginLinkAndData,
                    $this->_to,
                    $this->footerContent,
                    '',
                    $newsletterHeader['email_header']
                );
                $message = $this->view->translate('Newsletter has been sent successfully');
            } catch (Mandrill_Error $e) {
                $message = $this->view->translate('There is some problem in your data');
            }
            $flashMessage->addMessage(array('success' => $message));
            $this->_helper->redirector('emailcontent', 'accountsetting', null);
        } else {
            $this->_helper->redirector('index', 'index', null);
        }
        exit;
    }

    public function emailHeaderFooterAction()
    {
        $headerFooterContent = mysqli_real_escape_string(
            FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
            BackEnd_Helper_viewHelper::stripSlashesFromString(
                $this->getRequest()->getParam('data')
            )
        );

        switch($this->getRequest()->getParam('template')) {
            case 'email-header':
                Signupmaxaccount::updateHeaderContent($headerFooterContent);
                break;
            case 'email-footer':
                Signupmaxaccount::updateFooterContent($headerFooterContent);
                break;
        }
        die;
    }

    public function emailcontentAction()
    {
        $data = Signupmaxaccount::getAllMaxAccounts();

        $this->view->data = $data;
        $this->view->localeSettings = LocaleSettings::getLocaleSettings();
        $this->view->rights = $this->_settings['administration'];
        $this->view->timezones_list = Signupmaxaccount::$timezones;
        $this->view->newsletterHeaderImage = Newsletterbanners::getHeaderOrFooterImage('H');
        $this->view->newsletterFooterImage = Newsletterbanners::getHeaderOrFooterImage('F');
    }

    public function saveemailcontentAction()
    {
        # sanitize data
        $val = mysqli_real_escape_string(
            FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
            BackEnd_Helper_viewHelper::stripSlashesFromString(
                $this->getRequest()->getParam('val')
            )
        );

        switch ($this->getRequest()->getParam('name'))
        {
            case 'senderEmail':
                $senderEmail = Doctrine_Query::create()
                                ->update('Signupmaxaccount')
                                ->set('emailperlocale', '"'. $val .'"')
                                ->execute();
                break;
            case 'senderName':
                $senderEmail = Doctrine_Query::create()
                                ->update('Signupmaxaccount')
                                ->set('sendername', '"'. $val .'"')
                                ->execute();
                break;
            case 'emailSubject':
                $senderEmail = Doctrine_Query::create()
                                ->update('Signupmaxaccount')
                                ->set('emailsubject', '"'. $val .'"')
                                ->execute();
                break;
            case 'testEmail':
                $senderEmail = Doctrine_Query::create()
                                ->update('Signupmaxaccount')
                                ->set('testemail', '"'.$val.'"')
                                ->execute();
                break;
        }
        die;
    }


    public function saveTestimonialsAction()
    {


        if($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');

        }

        # sanitize data
        $content =  mysql_escape_string(
                         BackEnd_Helper_viewHelper::stripSlashesFromString(
                                $this->getRequest()->getParam('content') ));


        switch ($this->getRequest()->getParam('type')){
            case 'testimonial1':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('testimonial1', '"'. $content .'"')
                ->execute();
                break;
            case 'testimonial2':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('testimonial2','"'. $content.'"')
                ->execute();
                break;
            case 'testimonial3':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('testimonial3','"'. $content .'"')
                ->execute();
                break;
            case 'showTestimonial':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('showTestimonial', $content)
                ->execute();
                break;

        }
        die;

    }

    public function totalRecepientsAction()
    {

         if($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');

        }


        $visitors = Doctrine_Query::create()->select('count(id) as recepients')
                    ->from('Visitor v')
                    ->where('status = 1')
                    ->andWhere('active = 1')
                    ->andWhere('weeklyNewsLetter = 1')
                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        echo $this->_helper->json(array('recepients' => $visitors['recepients']), true);
    }

    public function disablemandrillnewsletterAction()
    {
        if ($this->_request->isPost()) {
            $flash = $this->_helper->getHelper('FlashMessenger');

            if (Signupmaxaccount::disableNewsletterSchedulingStatus()) {
                $flash->addMessage(array('success' => $this->view->translate('Newsletter schedule has been successfully disabled')));
            }

            $this->_helper->redirector('emailcontent' , 'accountsetting' , null );
        }
    }

    public function updateHeaderImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                if (isset($_FILES['newsLetterHeaderImage']['name']) && $_FILES['newsLetterHeaderImage']['name'] != '') {
                    $parmas = $this->_getAllParams();
                    $result = Newsletterbanners::updateNewsletterImages($parmas, 'header');
                    $this->_helper->json($result);
                }
            }
        }
        exit();
    }

    public function updateFooterImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                if (isset($_FILES['newsLetterFooterImage']['name']) && $_FILES['newsLetterFooterImage']['name'] != '') {
                    $parmas = $this->_getAllParams();
                    $result = Newsletterbanners::updateNewsletterImages($parmas, 'footer');
                    $this->_helper->json($result);
                }
            }
        }
        exit();
    }

    public function deleteNewletterBannerImagesAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                $parmas = $this->_getAllParams();
                $result = Newsletterbanners::deleteNewsletterImages($parmas, $parmas['imageType']);
                $this->_helper->json($result);
            }
        }
        exit();
    }
}
