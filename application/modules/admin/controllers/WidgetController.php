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
        $dbConnection = BackEnd_Helper_viewHelper::addConnection();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($dbConnection);
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
            if (\KC\Repository\Widget::addWidget($this->_getAllParams())) {
                self::addFlashMessage('Widget has been added successfully', 'success', HTTP_PATH.'admin/widget');
            } else {
                self::addFlashMessage('Problem in your data', 'error', HTTP_PATH.'admin/widget');
            }
        }
    }

    public function widgetlistAction()
    {
        $widgetList = \KC\Repository\Widget::getWidgetList($this->_getAllParams());
        echo Zend_Json::encode($widgetList);
        exit();
    }

    public function onlinestatusAction()
    {
        $widgetId = \KC\Repository\Widget::changeStatus($this->_getAllParams());
        self::updateVarnish($widgetId);
        echo Zend_Json::encode($widgetId);
        exit();
    }

    public function editwidgetAction()
    {
        $widgetId = intval($this->getRequest()->getParam('id'));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $parameters = $this->_getAllParams();
        if (intval($widgetId) > 0) {
            $widgetInformation = \KC\Repository\Widget::getWidgetInformation($widgetId);
            $this->view->widgetInformation = $widgetInformation;
            $this->view->id = $widgetId;
            if (!$widgetInformation['showWithDefault']) {
                $url = HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString');
                self::addFlashMessage('This Widget has default widget', 'error', $url);
            }
        }
        if ($this->_request->isPost()) {
            self::updateWidget($parameters);
        }
        if (!empty($parameters['delete'])) {
            self::deleteWidget($parameters['id']);
        }
    }

    public function updateWidget($parameters)
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $widget = new \KC\Repository\Widget();
        if ($widget->updateWidget($parameters)) {
            self::updateVarnish($id);
            $url = HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString');
            self::addFlashMessage('Widget has been updated successfully', 'success', $url);
        } else {
            $url = HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString');
            self::addFlashMessage('Problem in your data', 'error', $url);
        }
    }

    public function deleteWidget($widgetId)
    {
        $widget = new \KC\Repository\Widget();
        if ($widget->permanentDeleteWidget($widgetId)) {
            self::addFlashMessage('Widget has been deleted successfully', 'success', HTTP_PATH.'admin/widget');
        } else {
            self::addFlashMessage('Problem in your data', 'error', HTTP_PATH.'admin/widget');
        }
    }

    public function addFlashMessage($message, $errorType, $redirectUrl)
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate($message);
        $flash->addMessage(array($errorType => $message));
        $this->_redirect($redirectUrl);
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
        exit();
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

    public function sortWidgetAction()
    {
        $widgetType = $this->getRequest()->getParam('widgetType');
        $backEndHelper = new BackEnd_Helper_viewHelper();
        $widgetCategories = $backEndHelper->widgetCategories();
        if (isset($widgetType)) {
            $widgetType = $this->getRequest()->getParam('widgetType');
        } else {
            $widgetType = key($widgetCategories);
        }
        $categoryWidgets = \KC\Repository\PageWidgets::getWidgetsByType($widgetType);
        $widgetsIds = array();
        if (!empty($categoryWidgets)) {
            $widgetsIds = self::getWidgetIds($categoryWidgets);
        }
        $widgetsList = \KC\Repository\Widget::getUserDefinedwidgetList($widgetsIds);
        $this->view->widgetCategories = $widgetCategories;
        $this->view->widgetsList = $widgetsList;
        $this->view->widgetType = $widgetType;
        $this->view->categoryWidgets = $categoryWidgets;
    }

    public function getWidgetIds($categoryWidgets)
    {
        $widgetsIds = array();
        foreach ($categoryWidgets as $categoryWidget) {
            if (!empty($categoryWidget['widget'])) {
                $widgetsIds[] = $categoryWidget['widget']['id'];
            }
        }
        return $widgetsIds;
    }

    public function addWidgetInSortListAction()
    {
        $this->_helper->layout->disableLayout();
        $widgetId = $this->getRequest()->getParam('id');
        $widgetType = $this->getRequest()->getParam('widgetType');
        $result = \KC\Repository\PageWidgets::addWidgetInList($widgetId, $widgetType);
        echo Zend_Json::encode($result);
        exit();
    }
 
    public function deleteWidgetAction()
    {
        $this->_helper->layout->disableLayout();
        $pageWidgetId = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $widgetType = $this->getRequest()->getParam('widgetType');
        $isUpdated = \KC\Repository\PageWidgets::deleteWidget($pageWidgetId, $position, $widgetType);
        $widgets = \KC\Repository\PageWidgets::getWidgetsByType($widgetType);
        echo Zend_Json::encode($widgets);
        exit();
    }

    public function savePositionAction()
    {
        $this->_helper->layout->disableLayout();
        $widgetType = $this->getRequest()->getParam('widgetType');
        \KC\Repository\PageWidgets::savePosition($this->getRequest()->getParam('offersIds'), $widgetType);
        $widgets = \KC\Repository\PageWidgets::getWidgetsByType($widgetType);
        echo Zend_Json::encode($widgets);
        exit();
    }
}
