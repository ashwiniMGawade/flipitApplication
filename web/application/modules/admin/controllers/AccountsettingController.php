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


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new \Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'] ;


        if (! $this->getRequest()->isXmlHttpRequest()) {

            # add action as new case which needs to be viewed by other users
            switch(strtolower($this->view->action)) {
                case 'mandrill':
                break;
                case 'emailcontent':
                default:
                    if ($this->_settings['system manager']['rights'] != '1') {
                        $this->_redirect('/admin/auth/index');
                    }

            }

        } else {

            # add action as new case which needs to be viewed by other users
            switch(strtolower($this->view->action)) {
                case 'madrill':
                case 'changemailconfirmation':
                case 'saveemailcontent':
                case 'email-header-footer':
                break;
                default:
                    if ($this->_settings['system manager']['rights'] != '1') {
                        $this->getResponse()->setHttpResponseCode(404);
                        $this->_helper->redirector('index', 'index', null);
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
        $store_data = \KC\Repository\Signupfavoriteshop::getalladdstore();
        $this->view->store_data = $store_data;
        $data = \KC\Repository\Signupcodes::getfreeCodelogin();
        $this->view->codelogindata = $data;
        $maxacc_data = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->view->maxacc_data = $maxacc_data;
    }



    /**
     * Change email confimation status on click on yes no button
     */
    public function changemailconfirmationAction()
    {
        $status=$this->getRequest()->getParam("status");
        \KC\Repository\Signupmaxaccount::changeEmailConfimationSetting($status);
        die;
    }

    public function mandrillAction()
    {
        if ($this->_request->isPost()) {
            $flashMessage = $this->_helper->getHelper('FlashMessenger');
            $isScheduled = $this->getRequest()->getParam("isScheduled", false);
            if ($isScheduled) {
                $messageStatusResult = \KC\Repository\Signupmaxaccount::saveScheduledNewsletter($this->getRequest());
                switch ($messageStatusResult) {
                    case '1':
                        \KC\Repository\NewsLetterCache::saveNewsLetterCacheContent();
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
            $topVouchercodes = Application_Service_Factory::topOffers(10);
            $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('10_popularCategories_list');
            if ($categoryflag) {
                $topCategories = array_slice(\FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);
                \FrontEnd_Helper_viewHelper::setInCache('10_popularCategories_list', $topCategories);
            } else {
                $topCategories = \FrontEnd_Helper_viewHelper::getFromCacheByKey('10_popularCategories_list');
            }
           
            $newsLetterHeaderImage = \KC\Repository\Newsletterbanners::getHeaderOrFooterImage('header');
            $newsLetterHeaderImage = !empty($newsLetterHeaderImage) ? $newsLetterHeaderImage : '';
            $newsLetterFooterImage = \KC\Repository\Newsletterbanners::getHeaderOrFooterImage('footer');
            $newsLetterFooterImage = !empty($newsLetterFooterImage) ? $newsLetterFooterImage : '';
            $emailDetails = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();

            $mandrillSenderEmailAddress = $emailDetails[0]['emailperlocale'];
            $mandrillNewsletterSubject = $emailDetails[0]['emailsubject'];
            $mandrillSenderName = $emailDetails[0]['sendername'];
            \BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this);
            \BackEnd_Helper_MandrillHelper::getHeaderFooterContent($this);
            $mandrill = new Mandrill_Init($this->getInvokeArg('mandrillKey'));
            $categoryVouchers = array_slice(\KC\Repository\Category::getCategoryVoucherCodes($topCategories[0]['category']['id']), 0, 3);
            $categoryName = $topCategories[0]['category']['name'];
            $categoryPermalink = $topCategories[0]['category']['permaLink'];
            $newsletterHeader = \KC\Repository\Signupmaxaccount::getEmailHeaderFooter();
            try {
                \FrontEnd_Helper_viewHelper::sendMandrillNewsletterByBatch(
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
                    $newsletterHeader[0]['email_header'],
                    '',
                    $newsLetterHeaderImage,
                    $newsLetterFooterImage
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
        $headerFooterContent = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            $this->getRequest()->getParam('data')
        );

        switch($this->getRequest()->getParam('template')) {
            case 'email-header':
                \KC\Repository\Signupmaxaccount::updateHeaderContent($headerFooterContent);
                break;
            case 'email-footer':
                \KC\Repository\Signupmaxaccount::updateFooterContent($headerFooterContent);
                break;
        }
        die;
    }

    public function emailcontentAction()
    {
        $data = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->view->data = $data;
        $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        $this->view->rights = $this->_settings['administration'];
        $this->view->timezones_list = \KC\Repository\Signupmaxaccount::$timezones;
        $this->view->newsletterHeaderImage = \KC\Repository\Newsletterbanners::getHeaderOrFooterImage('header');
        $this->view->newsletterFooterImage = \KC\Repository\Newsletterbanners::getHeaderOrFooterImage('footer');
        $this->view->newsletterHeaderImageUrl = \KC\Repository\Newsletterbanners::getHeaderOrFooterImageUrl('headerurl', 'header');
        $this->view->newsletterFooterImageUrl = \KC\Repository\Newsletterbanners::getHeaderOrFooterImageUrl('footerurl', 'footer');

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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        switch ($this->getRequest()->getParam('name')){
            case 'senderEmail':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sa')
                ->set('sa.emailperlocale', "'".$val."'")
                ->getQuery()->execute();
                break;
            case 'senderName':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sm')
                ->set('sm.sendername', "'".$val."'")
                ->getQuery()->execute();
                break;
            case 'emailSubject':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'su')
                ->set('su.emailsubject', "'".$val."'")
                ->getQuery()->execute();
                break;
            case 'testEmail':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sx')
                ->set('sx.testemail', "'".$val."'")
                ->getQuery()->execute();
                break;
        }
        die;
    }

    public function saveTestimonialsAction()
    {
        if ($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');
        }
        # sanitize data
        $content = $this->getRequest()->getParam('content');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        switch ($this->getRequest()->getParam('type')){
            case 'testimonial1':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sa')
                ->set('sa.testimonial1', "'".$content."'")
                ->getQuery()->execute();
                break;
            case 'testimonial2':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sm')
                ->set('sm.testimonial2', "'".$content."'")
                ->getQuery()->execute();
                break;
            case 'testimonial3':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'su')
                ->set('su.testimonial3', "'".$content."'")
                ->getQuery()->execute();
                break;
            case 'showTestimonial':
                $queryBuilder ->update('\Core\Domain\Entity\Signupmaxaccount', 'sx')
                ->set('sx.showTestimonial', "'".$content."'")
                ->getQuery()->execute();
                break;
        }
        die;

    }

    public function totalRecepientsAction()
    {
        if ($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('count(v.id) as recepients')
        ->from('\Core\Domain\Entity\Visitor', 'v')
        ->where('v.status = 1')
        ->andWhere('v.active = 1')
        ->andWhere('v.weeklyNewsLetter = 1');
        $visitors = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        echo $this->_helper->json(array('recepients' => $visitors['recepients']), true);
    }

    public function disablemandrillnewsletterAction()
    {
        if ($this->_request->isPost()) {
            $flash = $this->_helper->getHelper('FlashMessenger');
            if (\KC\Repository\Signupmaxaccount::disableNewsletterSchedulingStatus()) {
                $flash->addMessage(array('success' => $this->view->translate('Newsletter schedule has been successfully disabled')));
            }
            $this->_helper->redirector('emailcontent', 'accountsetting', null);
        }
    }

    public function updateHeaderImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                if (isset($_FILES['newsLetterHeaderImage']['name']) && $_FILES['newsLetterHeaderImage']['name'] != '') {
                    $result = \KC\Repository\Newsletterbanners::updateNewsletterImages('header');
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
                    $result = \KC\Repository\Newsletterbanners::updateNewsletterImages('footer');
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
                $parameters = $this->_getAllParams();
                $result = \KC\Repository\Newsletterbanners::deleteNewsletterImages($parameters['imageType']);
                $this->_helper->json($result);
            }
        }
        exit();
    }

    public function saveNewsletterBannerImageUrlAction()
    {
        $columnValue = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('val'));
        $columnName =  FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('name'));
        \KC\Repository\Newsletterbanners::saveNewsletterImagesUrl($columnName, $columnValue);
        exit();
    }
}
