<?php
require_once 'Zend/Controller/Action.php';
class SignupController extends Zend_Controller_Action
{
    public $directLoginLinks = array();
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
        if (Auth_VisitorAdapter::hasIdentity()) {
            $this->_redirect(
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('link_profiel')
            );
        }
        $pageName = 'signup';
        $pageDetails =  Page::getPageDetails($pageName);
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->viewHelperObject->getMetaTags($this);
        
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
                        FrontEnd_Helper_viewHelper::__translate('Please change you E-mail address this user already exist'),
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
                        $visitorInformation['emailAddress']
                    );
                }
            } else {
                $registrationForm->highlightErrorElements();
            }
        }
        $testimonials = Signupmaxaccount::getTestimonials();
        $this->view->testimonials = $testimonials;
        $this->view->pageCssClass = 'register-page  home-page';
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
                    $message = FrontEnd_Helper_viewHelper::__translate('Please check your mail and confirm your email address');
                    $mandrillFunctions->sendConfirmationMail($visitorEmail, $this);
                } else {
                    Visitor::setVisitorLoggedIn($visitorId);
                    FrontEnd_Helper_viewHelper::redirectAddToFavouriteShop();
                    $message = FrontEnd_Helper_viewHelper::__translate('Thanks for registration now enjoy the more coupons');
                    $this->sendWelcomeMail($visitorId);
                }
                self::showFlashMessage(
                    $message,
                    HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_login'),
                    'success'
                );
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
                                'mailType' => 'welcome'
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
            $this->directLoginLinks
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
            if ($profileForm->isValid($this->getRequest()->getPost())) {
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
        $this->view->pageCssClass = 'profile-page  home-page';
        $this->view->firstName = $visitorDetailsForForm['firstName'];
        $this->viewHelperObject->getMetaTags($this);
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function addVisitor($visitorDetails)
    {
        $redirectLink =
            HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven'). '/' .
            FrontEnd_Helper_viewHelper::__link('link_profiel');
        $visitorId = Visitor::addVisitor($visitorDetails);
        if ($visitorId) {
            $message = FrontEnd_Helper_viewHelper::__translate('Your information has been updated successfully !.');
        } else {
            $message = FrontEnd_Helper_viewHelper::__translate('Please enter valid information');
        }
        self::redirectAccordingToMessage($visitorId, $message, $redirectLink, 'profile');
    }
}
