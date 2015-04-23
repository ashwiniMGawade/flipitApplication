<?php

class Admin_LocaleController extends Zend_Controller_Action
{
    protected $_settings = false;

    public function preDispatch()
    {
        $connectionInformation = \BackEnd_Helper_viewHelper::addConnection();

        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        \BackEnd_Helper_viewHelper::closeConnection($connectionInformation);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'];
    }
    public function localeSettingsAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
        $this->view->locale = KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->view->localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        $this->view->timezones_list = KC\Repository\Signupmaxaccount::$timezones;
        $this->view->localeStatus = KC\Repository\Website::getLocaleStatus($_COOKIE['site_name']);
        
        $this->view->chainHrefLang = KC\Repository\Website::getWebsiteDetails('', $_COOKIE['site_name']);

        if ($this->getRequest()->isPost()) {
            $chainParameters = $this->getRequest()->getParams();
            //echo "<pre>";print_r($this->view->chainParameters);die;
            KC\Repository\Website::saveChain($chainParameters['chain'], $_COOKIE['site_name']);
            $this->setFlashMessage($this, 'Chain has been updated successfully');
            $this->_redirect(HTTP_PATH . 'admin/locale/locale-settings');
        }
    }

    public function savelocaleAction()
    {
        $localeName = $this->getRequest()->getParam('locale');
        $localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        KC\Repository\LocaleSettings::savelocale($localeName);
        KC\Repository\Chain::updateChainItemLocale($localeName, $localeSettings[0]['locale']);
        $this->setFlashMessage($this, 'Locale has been changed successfully.');
        exit();
    }
    
    public function saveTimezoneAction()
    {
        KC\Repository\LocaleSettings::saveTimezone($this->getRequest()->getParam('timezone'));
        $this->setFlashMessage($this, 'Timezone has been changed successfully.');
        exit();
    }

    public function getlocaleAction()
    {
        $locale_data = KC\Repository\LocaleSettings::getLocaleSettings();
        echo Zend_Json::encode($locale_data[0]['locale']);
        exit();
    }

    public function savelocalestatusAction()
    {
        KC\Repository\Website::setLocaleStatus($this->getRequest()->getParam('localeStatus'), $_COOKIE['site_name']);
        $this->setFlashMessage($this, 'Locale Status has been changed successfully.');
        exit();
    }

    public function setFlashMessage($currentObject, $messageText)
    {
        $flash = $currentObject->_helper->getHelper('FlashMessenger');
        $message = $currentObject->view->translate($messageText);
        $flash->addMessage(array('success' => $message));
    }
}
