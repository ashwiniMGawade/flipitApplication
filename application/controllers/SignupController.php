<?php
require_once 'Zend/Controller/Action.php';
class SignupController extends Zend_Controller_Action
{
    public $_loginLinkAndData = array();
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
        exit;
    }

    public function indexAction()
    {
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical(
            FrontEnd_Helper_viewHelper::getPagePermalink()
        );
        if (Auth_VisitorAdapter::hasIdentity()) {
            $this->_redirect(
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('link_profiel')
            );
        }

        $pageDetails = Page::getPageDetailsFromUrl(FrontEnd_Helper_viewHelper::getPagePermalink());
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';

        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            '',
            FACEBOOK_IMAGE,
            ''
        );

        $emailAddressFromMemory = '';
        $emailAddressSpace = new Zend_Session_Namespace('emailAddressSignup');
        if (isset($emailAddressSpace->emailAddressSignup)) {
            $emailAddressFromMemory = $emailAddressSpace->emailAddressSignup;
        }
        
        $shopId = '';
        $shopIdNameSpace = new Zend_Session_Namespace('shopId');
        if (isset($shopIdNameSpace->shopId)) {
            $shopId = $shopIdNameSpace->shopId;
        }
        
        $registrationForm = new Application_Form_Register();
        $this->view->form = $registrationForm;
        $registrationForm->getElement('emailAddress')->setValue($emailAddressFromMemory);
        $registrationForm->getElement('shopId')->setValue($shopId);
        if ($this->getRequest()->isPost()) {
            if ($registrationForm->isValid($this->getRequest()->getPost())) {
                $visitorInformation = $registrationForm->getValues();
                if (Visitor::checkDuplicateUser($visitorInformation['emailAddress']) > 0) {
                    self::showFlashMessage(
                        FrontEnd_Helper_viewHelper::
                        __translate('Please change you E-mail address this user already exist'),
                        HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'),
                        'error'
                    );
                } else {
                    $visitorId = Visitor::addVisitor($visitorInformation);
                    if ($visitorId) {
                        Shop::shopAddInFavourite($visitorId, base64_decode($visitorInformation['shopId']));
                    }
                    self::redirectAccordingToMessage(
                        $visitorId,
                        FrontEnd_Helper_viewHelper::__translate('Please enter valid information'),
                        HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'),
                        'signup',
                        $visitorInformation['emailAddress'],
                        $visitorInformation['shopId']
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

    public function redirectAccordingToMessage(
        $visitorId,
        $message,
        $redirectLink,
        $pageName,
        $visitorEmail = '',
        $shopId = ''
    ) {
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
                    $message = FrontEnd_Helper_viewHelper::
                    __translate('Please check your mail and confirm your email address');
                    $mandrillFunctions->sendConfirmationMail($visitorEmail, $this);
                } else {
                    Visitor::setVisitorLoggedIn($visitorId);
                    FrontEnd_Helper_viewHelper::redirectAddToFavouriteShop();
                    $message = FrontEnd_Helper_viewHelper::
                    __translate('Thanks for registration now enjoy the more coupons');
                    $this->sendWelcomeMail($visitorId);
                }
                $redirectUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten');
                if (isset($shopId) && $shopId!='') {
                    $shopName = Shop::getShopName(base64_decode($shopId));
                    $message = $shopName. " ".  FrontEnd_Helper_viewHelper::
                    __translate('have been added to your favorite shops');
                    $redirectUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten');
                }
                self::showFlashMessage($message, $redirectUrl, 'success');
            }
        }
        return;
    }

    public function sendWelcomeMail($visitorId)
    {
        $visitorDetails = Visitor::getUserDetails($visitorId);
        $fromEmail = Signupmaxaccount::getEmailAddress();
        $mailer  = new FrontEnd_Helper_Mailer();
        $content = array(
                        'name'    => 'content',
                        'content' => $this->view->partial(
                            'emails/emailLayout.phtml',
                            array(
                                'topOffers' => Offer::getTopOffers(5),
                                'mailType' => 'welcome',
                                'firstName' => $visitorDetails[0]['firstName']
                                )
                        )
                    );
        $visitorName = $visitorDetails[0]['firstName'] .' '. $visitorDetails[0]['lastName'];
        BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this, 'frontend', $visitorDetails[0]['email']);
        $mailer->send(
            FrontEnd_Helper_viewHelper::__email('email_sitename'),
            $fromEmail[0]['emailperlocale'],
            $visitorName,
            $visitorDetails[0]['email'],
            FrontEnd_Helper_viewHelper::__email('email_Welcome e-mail subject'),
            $content,
            FrontEnd_Helper_viewHelper::__email('email_Welcome e-mail header'),
            '',
            $this->_loginLinkAndData
        );
        return true;
    }
    public function sendConfirmationMail($visitoremailMail)
    {
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/views/scripts/signup');
        $html->assign('email', $visitoremailMail);
        $bodyText = $html->render('confirmemail.phtml');
        $recipents = array("to" => $visitoremailMail);
        $subject = FrontEnd_Helper_viewHelper::__translate("Welcome to Kortingscode.nl");
        $body = $bodyText;
        BackEnd_Helper_viewHelper::SendMail($recipents, $subject, $body);
        return true;
    }

    public function profileAction()
    {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        if (!Auth_VisitorAdapter::hasIdentity()) {
            $this->_redirect('/');
        }
        $visitorDetails = Visitor::getUserDetails(Auth_VisitorAdapter::getIdentity()->id);
        $visitorDetailsForForm = $visitorDetails[0];
        $profileForm = new Application_Form_Profile();
        $this->view->form = $profileForm;
        if ($this->getRequest()->isPost()) {
            if ($profileForm->isValid($this->getRequest()->getPost())) {
                $visitorDetails = $profileForm->getValues();
                self::addVisitor($visitorDetails, 'profile');
            } else {
                $profileForm->highlightErrorElements();
            }
        } else {
            $dateOfBirth = array_reverse(explode('-', $visitorDetailsForForm['dateOfBirth']));
            $dateOfBirthDay = isset($dateOfBirth[0]) && $dateOfBirth[0] != '' ? $dateOfBirth[0] : '';
            $dateOfBirthMonth = isset($dateOfBirth[1]) && $dateOfBirth[1] != '' ? $dateOfBirth[1] : '';
            $dateOfBirthYear = isset($dateOfBirth[2]) && $dateOfBirth[2] != '' ? $dateOfBirth[2] : '';
            $profileForm->getElement('firstName')->setValue($visitorDetailsForForm['firstName']);
            $profileForm->getElement('lastName')->setValue($visitorDetailsForForm['lastName']);
            $profileForm->getElement('emailAddress')->setValue($visitorDetailsForForm['email']);
            $profileForm->getElement('gender')->setValue($visitorDetailsForForm['gender']);
            $profileForm->getElement('dateOfBirthDay')->setValue(isset($dateOfBirth[0]) && $dateOfBirth[0]=='00' ? '' : $dateOfBirthDay);
            $profileForm->getElement('dateOfBirthMonth')->setValue(isset($dateOfBirth[1]) && $dateOfBirth[1]=='00' ? '' : $dateOfBirthMonth);
            $profileForm->getElement('dateOfBirthYear')->setValue(isset($dateOfBirth[2]) && $dateOfBirth[2]=='0000' ? '' : $dateOfBirthYear);
            $profileForm->getElement('postCode')->setValue($visitorDetailsForForm['postalCode']);
            $profileForm->getElement('weeklyNewsLetter')->setValue($visitorDetailsForForm['weeklyNewsLetter']);
            $profileForm->getElement('codealert')->setValue($visitorDetailsForForm['codealert']);
        }
        $this->view->pageCssClass = 'profile-page';
        $this->view->firstName = $visitorDetailsForForm['firstName'];
        $this->viewHelperObject->getMetaTags($this);
  
    }

    public function addVisitor($visitorDetails, $profileUpdate = '')
    {
        $redirectLink =
            HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'). '/' .
            FrontEnd_Helper_viewHelper::__link('link_profiel');
        $visitorId = Visitor::addVisitor($visitorDetails, $profileUpdate);
        if ($visitorId) {
            $message = FrontEnd_Helper_viewHelper::__translate('Your information has been updated successfully !.');
        } else {
            $message = FrontEnd_Helper_viewHelper::__translate('Please enter valid information');
        }
        self::redirectAccordingToMessage($visitorId, $message, $redirectLink, 'profile');
    }

    public function signuplightboxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->shopLogo = $this->getRequest()->getParam('url');
        $this->view->shopId = $this->getRequest()->getParam('shopId');
        $this->view->shopName = Shop::getShopName(base64_decode($this->getRequest()->getParam('shopId')));
        $this->view->shopLightBoxText = Shop::getShopLightBoxText(base64_decode($this->getRequest()->getParam('shopId')));
        
    }

    public function signuplightboxsetsessionsAction()
    {
        $visitorInformation = intval(
            Visitor::checkDuplicateUser(
                $this->_getParam('emailAddress'),
                $this->_getParam('id')
            )
        );
        
        $this->_helper->layout->disableLayout();
        $params = $this->getRequest()->getParams();

        $visitorShopId = new Zend_Session_Namespace('shopId');
        $visitorShopId->shopId = $params['shopId'];
        if ($visitorInformation > 0) {
            $message = FrontEnd_Helper_viewHelper::__translate(
                'Your e-mail address is already known to us.
                 If you forgot your password, click here to change your password'
            );
            $forgotPasswordLink =
            HTTP_PATH_LOCALE .
            FrontEnd_Helper_viewHelper::__link('link_login').'/'
            .FrontEnd_Helper_viewHelper::__link('link_forgotpassword');

            self::showFlashMessage(
                $message ." " . "<a href='".$forgotPasswordLink."'>"
                . FrontEnd_Helper_viewHelper::__translate('forgot password') ."</a>",
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_login'),
                'error'
            );
            exit;
        }
        $visitorEmail = new Zend_Session_Namespace('emailAddressSignup');
        $visitorEmail->emailAddressSignup = $params['emailAddress'];
    
        $this->_redirect(HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'));
    }

    public function setsessionAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->getRequest()->getParams();
        $visitorShopId = new Zend_Session_Namespace('shopId');
        $visitorShopId->shopId = $params['shopId'];
        $this->_redirect(HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_login'));
    }

    
    public function signupwidgetAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->shopId = $this->getRequest()->getParam('shopId');
        $this->view->signupFormWidgetType = $this->getRequest()->getParam('signupFormWidgetType');
        $this->view->shopLogoOrDefaultImage = $this->getRequest()->getParam('shopLogoOrDefaultImage');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        $this->view->zendForm = $signUpFormSidebarWidget;
    }

    public function signupwidgetlargeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->shopId = $this->getRequest()->getParam('shopId');
        $this->view->signupFormWidgetType = $this->getRequest()->getParam('signupFormWidgetType');
        $this->view->shopLogoOrDefaultImage = $this->getRequest()->getParam('shopLogoOrDefaultImage');
        $signUpFormLargeForm = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $this->view->zendForm =  $signUpFormLargeForm;
    }

    public function signupwidgetfooterAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->shopId = $this->getRequest()->getParam('shopId');
        $this->view->signupFormWidgetType = $this->getRequest()->getParam('signupFormWidgetType');
        $this->view->shopLogoOrDefaultImage = $this->getRequest()->getParam('shopLogoOrDefaultImage');
        $signUpForm = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'footerLargeSignUpForm',
            'SignUp',
            'email-form form-inline',
            'orange'
        );
        $this->view->zendForm =  $signUpForm;
    }
}
