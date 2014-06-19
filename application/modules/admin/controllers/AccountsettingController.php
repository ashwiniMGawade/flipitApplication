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
    public $recipientMetaData  = array();

    public $headerMail = array();
    public $loginLinkAndData = array();
    public $to = array();
    public $staticContent = array();
    public $headerContent = array();
    public $footerContent = array();

    # holds settings regarding user rights
    protected $_settings = false ;
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
            $flash = $this->_helper->getHelper('FlashMessenger');
            $isScheduled = $this->getRequest()->getParam("isScheduled", false);

            if ($isScheduled) {
                if (Signupmaxaccount::saveScheduledNewsletter($this->getRequest())) {
                    $flash->addMessage(
                        array(
                            'success' => $this->view->translate('Newsletter has been successfully scheduled')
                        )
                    );
                } else {
                    $flash->addMessage(
                        array(
                            'error' => $this->view->translate('There is some problem in your data')
                        )
                    );
                }

                $this->_helper->redirector('emailcontent', 'accountsetting', null);
            }

            Signupmaxaccount::updateNewsletterSchedulingStatus();

            if (LOCALE == '') {
                $imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND, '/') .">
                    <img src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";
                $siteName = "Kortingscode.nl";
            } else {
                $imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND, '/') .">
                    <img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/></a>";
                $siteName = "Flipit.com";
            }

            set_time_limit(10000);
            ini_set('max_execution_time', 115200);
            ini_set("memory_limit", "1024M");

            $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list');
            if ($voucherflag) {
                $topVouchercodes = Offer::getTopOffers(10) ;
            } else {
                $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list');
            }

            $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularcategory_list');
            if ($categoryflag) {
                $topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);
                FrontEnd_Helper_viewHelper::setInCache('all_popularcategory_list', $topCategories);
            } else {
                $topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularcategory_list');
            }

            $email_data = Signupmaxaccount::getAllMaxAccounts();
            $mandrillSenderEmailAddress  = $email_data[0]['emailperlocale'];
            $mandrillNewsletterSubject  = $email_data[0]['emailsubject'];
            $mandrillSenderName  = $email_data[0]['sendername'];
            $this->getDirectLoginLinks();
            $this->getHeaderFooterContent();

            $this->headerMail = array(array('name' => 'headerMail',
                                            'content' => $imgLogoMail
                                     ),
                                    array('name' => 'headerContent',
                                            'content' => $this->headerContent
                                    ),
                                    array('name' => 'footerContent',
                                            'content' => $this->footerContent
                                    ));

            $this->staticContent = array(
                                    array('name' => 'websiteName',
                                            'content' => $siteName
                                    ),
                                    array('name' => 'unsubscribe',
                                            'content' => FrontEnd_Helper_viewHelper::__email('email_Uitschrijven')
                                    ),
                                    array('name' => 'editProfile',
                                            'content' => FrontEnd_Helper_viewHelper::__email('email_Wijzigen profiel')
                                    ),
                                    array('name' => 'contact',
                                            'content' => FrontEnd_Helper_viewHelper::__email('email_Contact')
                                    ),
                                    array('name' => 'contactLink',
                                            'content' => HTTP_PATH_FRONTEND . 'info/contact'
                                    ),
                                    array('name' => 'moreOffersLink',
                                        'content' => HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_populair')
                                    ),
                                    array('name' => 'moreOffers',
                                        'content' => FrontEnd_Helper_viewHelper::__email('email_Bekijk meer van onze top aanbiedingen') . ' >'
                                    )
                            );

            $mandrill = new Mandrill_Init($this->getInvokeArg('mandrillKey'));
            $templateName = $this->getInvokeArg('newsletterTemplate');
            $categoryVouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']), 0, 3);
            $categoryName = $topCategories[0]['category']['name'];
            try {
                FrontEnd_Helper_viewHelper::sendMandrillNewsletterByBatch(
                    $topVouchercodes,
                    $categoryVouchers,
                    $categoryName,
                    $mandrillNewsletterSubject,
                    $mandrillSenderEmailAddress,
                    $mandrillSenderName,
                    $this->recipientMetaData,
                    $this->loginLinkAndData,
                    $this->to,
                    $this->footerContent
                );
                $message = $this->view->translate('Newsletter has been sent successfully');
            } catch (Mandrill_Error $e) {
                $message = $this->view->translate('There is some problem in your data');

            }
            $flash->addMessage(array('success' => $message));
            $this->_helper->redirector('emailcontent', 'accountsetting', null);
        } else {
            $this->_helper->redirector('index', 'index', null);
        }
        die;
    }

    public function getDirectLoginLinks()
    {
        $email_data = Signupmaxaccount::getAllMaxAccounts();
        $testEmail = $this->getRequest()->getParam('testEmail');
        $dummyPass = MD5('12345678');
        $send = $this->getRequest()->getParam('send');
        $visitorData = array();
        $visitorMetaData = array();
        $toVisitorArray = array();

        if (isset($send) && $send == 'test') {

            $getTestEmaildata =  Visitor::getVisitorDetailsByEmail($testEmail);

            $key = 0;
            $visitorData[$key]['rcpt'] = $testEmail;
            $visitorData[$key][0]['name'] = 'loginLink';
            $visitorData[$key][0]['content'] =  HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("link_login") . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin") . "/" . base64_encode($getTestEmaildata[0]['email']) ."/". $getTestEmaildata[0]['password'];
            $visitorData[$key][1]['name'] = 'loginLinkWithUnsubscribe';
            $visitorData[$key][1]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("link_login") . "/" .FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe") . "/" . base64_encode($testEmail) ."/". $dummyPass;

            $toVisitorArray[$key]['email'] = $testEmail;
            $toVisitorArray[$key]['name'] = !empty($getTestEmaildata[0]['firstName']) ? $getTestEmaildata[0]['firstName'] . ' ' .$getTestEmaildata[0]['lastName'] : 'member';
            $this->loginLinkAndData = $visitorData;//set the visitor data in $loginLinkAndData array
            $this->to = $toVisitorArray;

        } else {
            if ($this->_settings['administration']['rights']  == 1) {
                $visitors = new Visitor();
                $visitors = $visitors->getVisitorsToSendNewsletter();
                $mandrill = new Mandrill_Init($this->getInvokeArg('mandrillKey'));
                $getUserDataFromMandrill = $mandrill->users->senders();

                foreach ($getUserDataFromMandrill as $key => $value) {
                    if ($value['soft_bounces'] >= 6 || $value['hard_bounces'] >= 2) {
                        $updateActive = Doctrine_Query::create()
                            ->update('Visitor')
                            ->set('active', 0)
                            ->where("email = '".$value['address']."'")
                            ->execute();
                    }
                }

                foreach ($visitors as $key => $value) {
                    $keywords ='' ;

                    foreach ($value['keywords'] as $k => $word) {

                        $keywords .= $word['keyword'] . ' ';
                    }

                    $visitorData[$key]['rcpt'] = $value['email'];
                    $visitorData[$key]['vars'][0]['name'] = 'loginLink';
                    $visitorMetaData[$key]['rcpt'] = $value['email'];
                    $visitorMetaData[$key]['values']['referrer'] = trim($keywords) ;
                    $visitorData[$key]['vars'][0]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("link_login") . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin") . "/" . base64_encode($value['email']) ."/". $value['password'];
                    $visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
                    $visitorData[$key]['vars'][1]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("link_login") . "/" .FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe") . "/" . base64_encode($value['email']) ."/". $value['password'];
                    $toVisitorArray[$key]['email'] = $value['email'];
                    $toVisitorArray[$key]['name'] = !empty($value['firstName']) ? $value['firstName'] : 'Member';

                }

                $this->recipientMetaData = $visitorMetaData;
                $this->loginLinkAndData = $visitorData;
                $this->to = $toVisitorArray;
            }
        }
    }


    /**
     * emailHeaderFooter
     *
     * save email header/footer content
     *
     * @author Surinderpal Singh
     */
    public function emailHeaderFooterAction()
    {
        # sanitize data
        $data = mysql_escape_string(
                BackEnd_Helper_viewHelper::stripSlashesFromString(
                        $this->getRequest()->getParam('data'))) ;


        # check tepmlete type
        switch($this->getRequest()->getParam('template')) {
            case 'email-header':
                # update headet template content
                Signupmaxaccount::updateHeaderContent($data);
            break;

            case 'email-footer':
                # update footer template content
                Signupmaxaccount::updateFooterContent($data);
            break;
        }

        die ;
    }
    public function getHeaderFooterContent()
    {
        $data = Signupmaxaccount::getEmailHeaderFooter();
        $this->headerContent = $data['email_header'];
        $this->footerContent = $data['email_footer'];
    }

    public function emailcontentAction()
    {
        $data = Signupmaxaccount::getAllMaxAccounts();

        $this->view->data = $data;

        $this->view->rights = $this->_settings['administration'];
        $this->view->timezones_list = Signupmaxaccount::$timezones;
    }

    public function saveemailcontentAction()
    {
        # sanitize data
        $val = mysql_escape_string(
                            BackEnd_Helper_viewHelper::stripSlashesFromString(
                                        $this->getRequest()->getParam('val'))) ;

        switch ($this->getRequest()->getParam('name')){
            case 'senderEmail':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                                ->set('emailperlocale','"'.	$val .'"')->execute();
            break;
            case 'senderName':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                            ->set('sendername','"'. $val .'"')->execute();
            break;
            case 'emailSubject':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                         ->set('emailsubject','"'. $val .'"')->execute();
            break;
            case 'testEmail':
                $senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
                          ->set('testemail','"'.$val.'"')->execute();
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

            if (Signupmaxaccount::disableNewsletterScheduling()) {
                $flash->addMessage(array('success' => $this->view->translate('Newsletter schedule has been successfully disabled')));
            }

            $this->_helper->redirector('emailcontent' , 'accountsetting' , null );
        }
    }

}
