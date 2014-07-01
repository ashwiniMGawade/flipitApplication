<?php

class Admin_LocaleController extends Zend_Controller_Action
{
    protected $_settings = false;

    public function preDispatch()
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection();
        $params = $this->_getAllParams();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'];
    }
    public function localeSettingsAction()
    {
        $role =  Zend_Auth::getInstance()->getIdentity()->roleId;
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
        $this->view->locale = Signupmaxaccount::getAllMaxAccounts();
        $this->view->localeSettings = LocaleSettings::getLocaleSettings();
        $this->view->timezones_list = Signupmaxaccount::$timezones;
        $this->view->localeStatus = Website::getLocaleStatus($_COOKIE['site_name']);
    }

    public function savelocaleAction()
    {
        LocaleSettings::savelocale($this->getRequest()->getParam('locale'));
        $this->setFlashMessage($this, 'Locale has been changed successfully.');
        die;
    }
    
    public function saveTimezoneAction()
    {

        LocaleSettings::saveTimezone($this->getRequest()->getParam('timezone'));
        $this->setFlashMessage($this, 'Timezone has been changed successfully.');
        die;
    }

    public function getlocaleAction()
    {
        $locale_data = LocaleSettings::getLocaleSettings();
        echo Zend_Json::encode($locale_data[0]['locale']);
        die;
    }

    public function savelocalestatusAction()
    {
        Website::setLocaleStatus($this->getRequest()->getParam('localeStatus'), $_COOKIE['site_name']);
        $this->setFlashMessage($this, 'Locale Status has been changed successfully.');
        die;
    }

    public function setFlashMessage($currentObject, $messageText)
    {
        $flash = $currentObject->_helper->getHelper('FlashMessenger');
        $message = $currentObject->view->translate($messageText);
        $flash->addMessage(array('success' => $message));
    }
}
