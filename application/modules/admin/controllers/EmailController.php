<?php

class Admin_EmailController extends Zend_Controller_Action
{

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author Amit Sharma
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
        $emailData = Emails::getAllEmailsContent($params);

        echo Zend_Json::encode($emailData);
        die();
    }


    /**
     * Get template data
     * @author asharma
     * @params template ID
     * @version 1.0
     */
    public function editEmailsAction()
    {
        $params = $this->_getAllParams();
        $this->view->offerId = $params['id'];

        $this->view->qstring = $_SERVER['QUERY_STRING'];

        $templateId = $params['id'];

        $templateData = Emails::getTemplateContent($templateId);
        //echo "<pre>";print_r($templateData);die;
        $this->view->templateId = $templateId;
        $this->view->templateData = $templateData;
        // end code
    }


    /**
     * emailHeaderFooter
     *
     * save email header/footer content
     *
     * @author Amit Sharma
     */
    public function emailHeaderFooterAction()
    {
        # sanitize data
        $data = mysql_escape_string(
                BackEnd_Helper_viewHelper::stripSlashesFromString(
                        $this->getRequest()->getParam('data'))) ;

       $templateId = $this->getRequest()->getParam('templateId');
        # check tepmlete type
        switch($this->getRequest()->getParam('template')) {
            case 'email-header':
                # update headet template content
                Emails::updateHeaderContent($data, $templateId );
            break;

            case 'email-footer':
                # update footer template content
                Emails::updateFooterContent($data, $templateId);
            break;
        }

        die ;
    }



      public function saveemailcontentAction()
    {
        # sanitize data
        $val = mysql_escape_string(
                            BackEnd_Helper_viewHelper::stripSlashesFromString(
                                        $this->getRequest()->getParam('val'))) ;

        $templateId =  $this->getRequest()->getParam('templateId');

        Emails::updateBodyContent($val, $templateId);

        die;
    }


      /**
     * mandrill
     *
     * This function initialize the mandrill and send the mail using mandrill template
     *
     * @author cbhopal
     * @version 1.0
     */
    public function mandrillAction()
    {
        if ($this->_request->isPost()) {
            //add the flash mesage that the newsletter has been sent
            $flash = $this->_helper->getHelper('FlashMessenger');

            $isScheduled = $this->getRequest()->getParam("isScheduled" , false);

            if($isScheduled) {
                if(Signupmaxaccount::saveScheduledNewsletter( $this->getRequest())) {
                    $flash->addMessage(array('success' => $this->view->translate('Newsletter has been successfully scheduled')));
                } else {
                    $flash->addMessage(array('error' => $this->view->translate('There is some problem in your data') ));
                }

                $this->_helper->redirector('emailcontent' , 'accountsetting' , null ) ;
            }

            # update current scheduled status to sent
            Signupmaxaccount::updateNewsletterSchedulingStatus();

            if(LOCALE == '') {
                $imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND , '/') ."><img src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";
                $siteName = "Kortingscode.nl";
            } else  {
                $imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND , '/') ."><img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/></a>";
                $siteName = "Flipit.com";
            }

            set_time_limit ( 10000 );
            ini_set('max_execution_time',115200);
            ini_set("memory_limit","1024M");

            //get offers from top ten popular shops and top one cateory as in homepage

            $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list');

            //key not exist in cache

            if($voucherflag){

                # get 10 popular vouchercodes for news letter
                $topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10) ;
                $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes,10);

            } else {
                $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list');
            }

            $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularcategory_list');

            //key not exist in cache

            if($categoryflag){

                $topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10),0,1);

                FrontEnd_Helper_viewHelper::setInCache('all_popularcategory_list', $topCategories);

            } else {

                $topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularcategory_list');

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
            $data = array_merge($voucherCodesData['dataShopName'],
                    $voucherCodesData['dataOfferName'],
                    $voucherCodesData['dataShopImage'],
                    $voucherCodesData['expDate'],
                    $this->headerMail, $this->dataShopNameCat,
                    $this->dataOfferNameCat, $this->dataShopImageCat,
                    $this->expDateCat, $this->category
            );

            //merge the permalinks array and static content array into single array
            $dataPermalink = array_merge($voucherCodesData['shopPermalink'], $this->shopPermalinkCat,
                                         $this->staticContent);

            //initialize mandrill with the template name and other necessary options
            $mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey'));
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
            $this->_helper->redirector('emailcontent' , 'accountsetting' , null ) ;
        } else {

            $this->_helper->redirector('index' , 'index' , null ) ;
        }
        die;

    }

    public function emailSettingsAction()
    {

    }
}
