<?php
class Admin_WidgetController extends Zend_Controller_Action
{
    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    public function preDispatch()
    {
        $dbConnection = \BackEnd_Helper_viewHelper::addConnection();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['content']['rights'] != '1') {
            $this->_redirect('/admin/auth/index');
        }
    }
    public function indexAction()
    {

    }
    public function addwidgetAction()
    {
        if ($this->_request->isPost()) {
            $parameters = $this->_getAllParams();
            $flash = $this->_helper->getHelper('FlashMessenger');
            if (\KC\Repository\Widget::addWidget($parameters)) {
                $message = $this->view->translate('widget has been added successfully');
                $flash->addMessage(array('success' => $message));
                $this->_helper->redirector(null, 'widget', null);
            } else {
                $message = $this->view->translate('Problem in your data.');
                $flash->addMessage(array('error' => $message));
                $this->_helper->redirector(null, 'widget', null);
            }
        }
    }

    public function widgetlistAction()
    {
        $parameters = $this->_getAllParams();
        $widgetList = \KC\Repository\Widget::getWidgetList($parameters);
        echo Zend_Json::encode($widgetList);
        die ();
    }

    public function onlinestatusAction()
    {
        $parameters = $this->_getAllParams();
        $widgetId = \KC\Repository\Widget::changeStatus($parameters);
        self::updateVarnish($widgetId);
        echo Zend_Json::encode($widgetId);
        die ();
    }

    public function editwidgetAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $widgetId = intval($this->getRequest()->getParam('id'));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $parameters = $this->_getAllParams();
        if (intval($widgetId) > 0) {
            $widgetInformation = \KC\Repository\Widget::updateWidget($widgetId);
            $this->view->widgetInformation = $widgetInformation;
            $this->view->id = $widgetId;
            if (!$widgetInformation['showWithDefault']) {
                $message = $this->view->translate('This Widget has default widget');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString'));
            }
        }
        if ($this->_request->isPost()) {
            self::updateWidget($parameters);
        }
        if (@$parameters['act'] == 'delete') {
            self::deleteWidget($parameters['id']);
        }
    }

    public function updateWidget($parameters)
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $widget = new \KC\Repository\Widget();
        if ($widget->editWidgetRecord($parameters)) {
            self::updateVarnish($id);
            $message = $this->view->translate('Widget has been updated successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect(HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString'));

        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
            $this->_redirect(HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString'));
        }
    }

    public function deleteWidget($widgetId)
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $widget = new \KC\Repository\Widget();
        $flash = $this->_helper->getHelper('FlashMessenger');
        if ($widget->permanentDeleteWidget($widgetId)) {
            $message = $this->view->translate('Widget has been deleted successfully');
            $flash->addMessage(array('success' => $message));
            $this->_helper->redirector(null, 'widget', null);
        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
            $this->_helper->redirector(null, 'widget', null);
        }
    }

    public function searchkeyAction()
    {
        $searchString = $this->getRequest()->getParam('keyword');
        $widgetList = \KC\Repository\Widget::searchKeyword($searchString);
        $widgets = array();
        if (sizeof($widgetList) > 0) {
            foreach ($widgetList as $widget) {
                    $widgets[] = ucfirst($widget['title']);
            }
        } else {
            $widgets[] = $this->view->translate("No Record Found");
        }
        echo Zend_Json::encode($widgets);
        die;
    }

    public function updateVarnish($id)
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishUrls = \KC\Repository\Widget::getAllUrls($id);
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $value);
            }
        }
    }
}
