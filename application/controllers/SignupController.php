<?php
require_once 'Zend/Controller/Action.php';
class SignupController extends Zend_Controller_Action
{
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
    }

    public function indexAction()
    {
        $this->view->pageCssClass = 'register-page';
        $registrationForm = new Application_Form_Register();
        $this->view->form = $registrationForm;
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
                    self::redirectWithSuccessOrErrorMessage(
                        $visitorId,
                        $this->view->translate('Please enter valid information'),
                        HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'),
                        'signup'
                    );
                }
            } else {
                $registrationForm->highlightErrorElements();
            }
        }
    }
 
    public function showFlashMessage($message, $redirectUrl, $messageType)
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array($messageType => $message));
        $this->_redirect($redirectUrl);
    }

    public function redirectWithSuccessOrErrorMessage($visitorId, $message, $redirectLink, $pageName)
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
                self::showFlashMessage(
                    $this->view->translate('Please check your mail and confirm your email address'),
                    HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('login'),
                    'success'
                );
            }
        }
    }

    public function profileAction()
    {
        if (!Auth_VisitorAdapter::hasIdentity()) {
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');
            $this->_redirect('/');
        }
        $visitorDetailsForForm = Visitor::getUserDetails(Auth_VisitorAdapter::getIdentity()->id);
        $visitorDetailsForForm = $visitorDetailsForForm[0];
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
            $profileForm->getElement('dateOfBirthDay')->setValue($dateOfBirth[0]);
            $profileForm->getElement('dateOfBirthMonth')->setValue($dateOfBirth[1]);
            $profileForm->getElement('dateOfBirthYear')->setValue($dateOfBirth[2]);
            $profileForm->getElement('postCode')->setValue($visitorDetailsForForm['postalCode']);
            $profileForm->getElement('weeklyNewsLetter')->setValue($visitorDetailsForForm['weeklyNewsLetter']);
        }
        $this->view->pageCssClass = 'profile-page';
        $this->view->firstName = $visitorDetailsForForm['firstName'];
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
        self::redirectWithSuccessOrErrorMessage($visitorId, $message, $redirectLink, 'profile');
    }
}
