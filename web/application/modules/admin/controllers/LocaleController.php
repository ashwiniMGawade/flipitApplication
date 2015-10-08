<?php

use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class Admin_LocaleController extends Application_Admin_BaseController
{
    protected $_settings = false;

    public function preDispatch()
    {
        $connectionInformation = \BackEnd_Helper_viewHelper::addConnection();

        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('/admin/auth/index');
        }

        \BackEnd_Helper_viewHelper::closeConnection($connectionInformation);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();

        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $message = $this->view->translate('You have no permission to access page');
            $this->setFlashMessage('success', $message);
            $this->redirect('/admin');
        }

        $this->_settings  = $sessionNamespace->settings['rights'];
    }
    public function localeSettingsAction()
    {
        $this->view->locale = KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->view->localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        $this->view->timezones_list = KC\Repository\Signupmaxaccount::$timezones;
        $site_name = "kortingscode.nl";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }
        $this->view->localeStatus = KC\Repository\Website::getLocaleStatus($site_name);
        
        $this->view->chainHrefLang = KC\Repository\Website::getWebsiteDetails('', $site_name);

        $this->view->settings = AdminFactory::getSettings()->execute(array('isEditable'=>1, 'deleted'=>0));

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();

            KC\Repository\Website::saveChain($params['chain'], $site_name);
            $isValid = true;
            if (true === is_array($params['settings'])) {
                foreach ($this->view->settings as $setting) {
                    if (true == array_key_exists($setting->getName(), $params['settings']) && $setting->getValue() != $params['settings'][$setting->getName()]) {
                        $data = array('value' => $params['settings'][$setting->getName()]);
                        $result = AdminFactory::updateSetting()->execute($setting, $data);
                        if ($result instanceof Errors) {
                            $this->setFlashMessage('error', $result->getErrorsAll());
                            $isValid = false;
                            break;
                        }
                    }
                }
            }
            if (true == $isValid) {
                $this->setFlashMessage('success', 'Locale settings has been updated successfully');
                $this->redirect(HTTP_PATH . 'admin/locale/locale-settings');
            }
        }
    }

    public function savelocaleAction()
    {
        $localeName = $this->getRequest()->getParam('locale');
        $localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        KC\Repository\LocaleSettings::savelocale($localeName);
        KC\Repository\Chain::updateChainItemLocale($localeName, $localeSettings[0]['locale']);
        $this->setFlashMessage('success', 'Locale has been changed successfully.');
        exit();
    }
    
    public function saveTimezoneAction()
    {
        KC\Repository\LocaleSettings::saveTimezone($this->getRequest()->getParam('timezone'));
        $this->setFlashMessage('success', 'Timezone has been changed successfully.');
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
        $this->setFlashMessage('success', 'Locale Status has been changed successfully.');
        exit();
    }
}
