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
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
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
                    self::redirectAccordingToUserStatus($visitorId, $visitorInformation);
                }
            } else {
                $registrationForm->highlightErrorElements();
            }
        }
    }

    public function redirectAccordingToUserStatus($visitorId, $visitorInformation)
    {
        if (!$visitorId) {
            self::showFlashMessage(
                $this->view->translate('Please enter valid information'),
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'),
                'error'
            );
        } else {
            $this->_redirect(
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('profiel') .'/' .
                base64_encode($visitorInformation['emailAddress'])
            );
        }
    }

    public function showFlashMessage($message, $redirectUrl, $messageType)
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $flash->addMessage(array($messageType => $message));
        $this->_redirect($redirectUrl);
    }

    public function profileAction()
    {
        die('WELCOME');
    }
}
