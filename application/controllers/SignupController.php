<?php
require_once 'Zend/Controller/Action.php';
class SignupController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function checkuserAction()
    {
        $visitorInformation = intval(
            Visitor::checkDuplicateUser(
                $this->_getParam('emailAddress'),
                $this->_getParam('id')
            )
        );
        $visitorStatus = $visitorInformation > 0 ? false : true;
        echo Zend_Json::encode($visitorStatus);
        die();
    }


    public function indexAction()
    {
        if (Auth_VisitorAdapter::hasIdentity()) {
            $this->_redirect(
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('profiel')
            );
        }
        $pageName = 'SignUp';
        $pageId = PageAttribute::getPageAttributeIdByName($pageName);
        $pageDetails =  Page::getPageFromFilteredPageAttribute($pageId);
        $this->view->pageTitle = $pageDetails['pageTitle'];
        $this->viewHelperObject->getMetaTags($this);
        
        $emailAddressFromMemory = '';
        $emailAddressSpace = new Zend_Session_Namespace('emailAddressSignup');
        if (isset($emailAddressSpace->emailAddressSignup)) {
            $emailAddressFromMemory = $emailAddressSpace->emailAddressSignup;
            //$emailAddressSpace->emailAddressSignup = '';
        }
        
        $shopId = '';
        $shopIdNameSpace = new Zend_Session_Namespace('shopId');
        if (isset($shopIdNameSpace->shopId)) {
            $shopId = $shopIdNameSpace->shopId;
            //$shopIdNameSpace->shopId = '';
        }
        
        $registrationForm = new Application_Form_Register();
        $this->view->form = $registrationForm;
        $registrationForm->getElement('emailAddress')->setValue($emailAddressFromMemory);
        $registrationForm->getElement('shopId')->setValue($shopId);
        if ($this->getRequest()->isPost()) {
            if ($registrationForm->isValid($_POST)) {
                $visitorInformation = $registrationForm->getValues();
                if (Visitor::checkDuplicateUser($visitorInformation['emailAddress']) > 0) {
                    self::showFlashMessage(
                        $this->view->translate('Please change you E-mail address this user already exist'),
                        HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'),
                        'error'
                    );
                } else {
                    $visitorId = Visitor::addVisitor($visitorInformation);
                    if ($visitorId) {
                        Shop::shopAddInFavourite($visitorId, base64_decode($visitorInformation['shopId']));
                    }
                    self::redirectAccordingToMessage(
                        $visitorId,
                        $this->view->translate('Please enter valid information'),
                        HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'),
                        'signup',
                        $visitorInformation['emailAddress']
                    );
                }
            } else {
                $registrationForm->highlightErrorElements();
            }
        }
        $testimonials = Signupmaxaccount::getTestimonials();
        $this->view->testimonials = $testimonials;
        $this->view->pageCssClass = 'register-page';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function showFlashMessage($message, $redirectUrl, $messageType)
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array($messageType => $message));
        $this->_redirect($redirectUrl);
    }

    public function redirectAccordingToMessage($visitorId, $message, $redirectLink, $pageName, $visitorEmail = '')
    {
        if (!$visitorId) {
            self::showFlashMessage(
                $message,
                $redirectLink,
                'error'
            );
        } else {
            if ($pageName!='signup') {
                self::showFlashMessage(
                    $message,
                    $redirectLink,
                    'success'
                );
            } else {
                $mandrillFunctions = new FrontEnd_Helper_MandrillMailFunctions();
                if (Signupmaxaccount::getemailConfirmationStatus()) {
                    $message = $this->view->translate('Please check your mail and confirm your email address');
                    $mandrillFunctions->sendConfirmationMail($visitorEmail, $this);
                } else {
                    Visitor::setVisitorLoggedIn($visitorId);
                    $message = $this->view->translate('Thanks for registration now enjoy the more coupons');
                    $mandrillFunctions->sendWelcomeMail($visitorId, $this);
                }
                self::showFlashMessage(
                    $message,
                    HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('login'),
                    'success'
                );
            }
        }
        return;
    }

    public function sendConfirmationMail($visitoremailMail)
    {
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/views/scripts/signup');
        $html->assign('email', $visitoremailMail);
        $bodyText = $html->render('confirmemail.phtml');
        $recipents = array("to" => $visitoremailMail);
        $subject = $this->view->translate("Welcome to Kortingscode.nl");
        $body = $bodyText;
        $sendEmail = BackEnd_Helper_viewHelper::SendMail($recipents, $subject, $body);
        return true;
    }

    public function profileAction()
    {
        if (!Auth_VisitorAdapter::hasIdentity()) {
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');
            $this->_redirect('/');
        }
        $visitorDetails = Visitor::getUserDetails(Auth_VisitorAdapter::getIdentity()->id);
        $visitorDetailsForForm = $visitorDetails[0];
        $profileForm = new Application_Form_Profile();
        $this->view->form = $profileForm;
        if ($this->getRequest()->isPost()) {
            if ($profileForm->isValid($_POST)) {
                $visitorDetails = $profileForm->getValues();
                self::addVisitor($visitorDetails);
            } else {
                $profileForm->highlightErrorElements();
            }
        } else {
            $dateOfBirth = array_reverse(explode('-', $visitorDetailsForForm['dateOfBirth']));
            $profileForm->getElement('firstName')->setValue($visitorDetailsForForm['firstName']);
            $profileForm->getElement('lastName')->setValue($visitorDetailsForForm['lastName']);
            $profileForm->getElement('emailAddress')->setValue($visitorDetailsForForm['email']);
            $profileForm->getElement('gender')->setValue($visitorDetailsForForm['gender']);
            $profileForm->getElement('dateOfBirthDay')->setValue($dateOfBirth[0]=='00' ? '' : $dateOfBirth[0]);
            $profileForm->getElement('dateOfBirthMonth')->setValue($dateOfBirth[1]=='00' ? '' : $dateOfBirth[1]);
            $profileForm->getElement('dateOfBirthYear')->setValue($dateOfBirth[2]=='0000' ? '' : $dateOfBirth[2]);
            $profileForm->getElement('postCode')->setValue($visitorDetailsForForm['postalCode']);
            $profileForm->getElement('weeklyNewsLetter')->setValue($visitorDetailsForForm['weeklyNewsLetter']);
        }
        $this->view->pageCssClass = 'profile-page';
        $this->view->firstName = $visitorDetailsForForm['firstName'];
        $this->viewHelperObject->getMetaTags($this);
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function addVisitor($visitorDetails)
    {
        $redirectLink =
            HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'). '/' .
            FrontEnd_Helper_viewHelper::__link('profiel');
        $visitorId = Visitor::addVisitor($visitorDetails);
        if ($visitorId) {
            $message = $this->view->translate('Your information has been updated successfully !.');
        } else {
            $message = $this->view->translate('Please enter valid information');
        }
        self::redirectAccordingToMessage($visitorId, $message, $redirectLink, 'profile');
    }
}
