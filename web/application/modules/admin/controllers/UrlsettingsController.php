<?php
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class Admin_UrlsettingsController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $this->redirect('/admin');
        }
    }

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
        $urlSettings = AdminFactory::getURLSettings()->execute();
        $urlSettingsData = array();
        foreach ($urlSettings as $urlSetting) {
            $urlSettingsData[] = array(
                'id' => $urlSetting->getId(),
                'status' => $urlSetting->getStatus(),
                'url' => $urlSetting->getUrl()
            );
        }

        $response = \DataTable_Helper::createResponse(1, $urlSettingsData, count($urlSettings));
        echo Zend_Json::encode($response);
        exit;
    }

    public function createAction()
    {
        if ($this->_request->isPost()) {
            $urlSetting = AdminFactory::createURLSettings()->execute();

            $parameters['status'] = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('status')));
            $parameters['url'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('url'));

            $result = AdminFactory::addURLSetting()->execute($urlSetting, $parameters);

            if ($result instanceof Errors) {
                $this->view->urlSetting = $this->getAllParams();
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->setFlashMessage('success', 'VWO Tag has been added successfully');
                $this->redirect(HTTP_PATH.'admin/urlsettings');
            }
        }
    }

    public function editAction()
    {
        $urlSettingId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('id')));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if ($urlSettingId <= 0) {
            $this->setFlashMessage('error', 'Invalid Id provided.');
            $this->redirect(HTTP_PATH.'admin/urlsettings');
        }
        $result = AdminFactory::getURLSetting()->execute(array('id' => $urlSettingId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH.'admin/urlsettings');
        } else {
            $urlSetting = $result;
            $this->view->id = $urlSettingId;
            $url = $urlSetting->getUrl();

            $urlSettingInfo = array(
                'id' => $urlSetting->getId(),
                'status' => $urlSetting->getStatus(),
                'url' => $urlSetting->getUrl()
            );
            $this->view->urlSetting = $urlSettingInfo;

            if ($this->_request->isPost()) {
                $parameters['status'] = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('status')));
                $parameters['url'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('url'));

                $result = AdminFactory::updateURLSetting()->execute($urlSetting, $parameters);

                if ($result instanceof Errors) {
                    $this->view->urlSetting = $this->getAllParams();
                    $errors = $result->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    self::updateVarnish($url);
                    $this->setFlashMessage('success', 'VWO Tag has been added successfully');
                    $this->redirect(HTTP_PATH.'admin/urlsettings');
                }
            }
        }
    }

    public function deleteAction()
    {
        $urlSettingId = intval($this->getRequest()->getParam('id'));
        if (intval($urlSettingId) < 1) {
            $this->setFlashMessage('error', 'Invalid id provided.');
            exit;
        }

        $result = AdminFactory::getURLSetting()->execute(array('id' => $urlSettingId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            exit;
        }
        AdminFactory::deleteURLSetting()->execute($result);
        self::updateVarnish($result->getUrl());
        $this->setFlashMessage('success', 'VWO Tag deleted successfully.');
        exit;
    }

    public function updateVarnish($url)
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND.$url);
    }

    public function validateurlAction()
    {
        $isValid = false;
        $url = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('url'));
        $urlSettingId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('editId')));

        $conditions = array(
            'url' => $url
        );
        $urlSettings = AdminFactory::getURLSettings()->execute($conditions);

        if (count($urlSettings) == 0) {
            $isValid = true;
        } elseif (count($urlSettings) == 1 && $urlSettings[0]->getId() == $urlSettingId) {
            $isValid = true;
        }
        echo Zend_Json::encode($isValid);
        exit;
    }
}
