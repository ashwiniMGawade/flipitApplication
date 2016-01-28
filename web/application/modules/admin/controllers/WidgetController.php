<?php

use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class Admin_WidgetController extends Application_Admin_BaseController
{
    public function init()
    {
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
            $widget = AdminFactory::createWidget()->execute();
            $result = AdminFactory::addWidget()->execute($widget, $this->getAllParams());
            $this->view->widget = $this->getAllParams();
            if ($result instanceof Errors) {
                $this->view->widget = $this->getAllParams();
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->setFlashMessage('success', 'Widget has been added successfully');
                $this->redirect(HTTP_PATH.'admin/widget');
            }
        }
    }

    public function widgetlistAction()
    {
        $widgetList = \KC\Repository\Widget::getWidgetList($this->getAllParams());
        echo Zend_Json::encode($widgetList);
        exit();
    }

    public function onlinestatusAction()
    {
        $widgetId = \KC\Repository\Widget::changeStatus($this->getAllParams());
        self::updateVarnish();
        echo Zend_Json::encode($widgetId);
        exit();
    }

    public function editwidgetAction()
    {
        $widgetId = intval($this->getRequest()->getParam('id'));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $parameters = $this->getAllParams();
        if (intval($widgetId) < 1) {
            $this->setFlashMessage('error', 'Invalid widget id provided.');
            $this->redirect(HTTP_PATH.'admin/widget');
        }
        $result = AdminFactory::getWidget()->execute(array('id' => $widgetId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH.'admin/widget');
        } else {
            $widget = $result;
            $this->view->id = $widgetId;
            if (!$widget->getShowWithDefault()) {
                $url = HTTP_PATH . 'admin/widget#' . $this->getRequest()->getParam('qString');
                $this->setFlashMessage("error", "You can't modify default widget.");
                $this->redirect($url);
            }

            if ($this->_request->isPost()) {

                $result = AdminFactory::updateWidget()->execute($widget, $parameters);
                if ($result instanceof Errors) {
                    $errors = $result->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    $widget = $result;
                    self::updateVarnish();
                    \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
                    $this->setFlashMessage('success', 'Widget has been updated successfully');
                    $url = HTTP_PATH . 'admin/widget#' . $this->getRequest()->getParam('qString');
                    $this->redirect($url);
                }
            }
            $widgetInformation = array(
                'title' => $widget->getTitle(),
                'content' => $widget->getContent(),
                'startDate' => $widget->getStartDate(),
                'endDate' => $widget->getEndDate()
            );
            $this->view->widgetInformation = $widgetInformation;

            if (!empty($parameters['delete'])) {
                self::deleteWidget($parameters['id']);
            }
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
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate($message);
        $flashMessenger->addMessage(array($errorType => $message));
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

    public function updateVarnish()
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishUrls = \KC\Repository\Widget::getAllUrls();
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $value);
            }
        }
    }
    private function _getWidgetTypeList($widgetType)
    {
        $widgetTypeList = [];
        switch ($widgetType) {
            case "categories":
                $categories =  \KC\Repository\Category::getCategoryList();
                if (!empty($categories['aaData'])) {
                    foreach ($categories['aaData'] as $category) {
                        if ($category['status']) {
                            $widgetTypeList[$category['id']] = $category['name'];
                        }
                    }
                }
                break;
            case "special-page":
                $specialPageList =  \KC\Repository\Page::getSpecialListPages();
                if (!empty($specialPageList)) {
                    foreach ($specialPageList as $specialPage) {
                        $widgetTypeList[$specialPage['id']] = $specialPage['pageTitle'];
                    }
                }
                break;
            default:
        }
        return $widgetTypeList;
    }

    public function sortWidgetAction()
    {
        $widgetType = $this->getRequest()->getParam('widgetType');
        $widgetCategoryType = $this->getRequest()->getParam('widgetCategoryType');
        $backEndHelper = new BackEnd_Helper_viewHelper();
        $widgetCategories = $backEndHelper->widgetCategories();
        if (isset($widgetType)) {
            $widgetType = $this->getRequest()->getParam('widgetType');
        } else {
            $widgetType = key($widgetCategories);
        }

        if (isset($widgetCategoryType) AND !empty($widgetCategoryType)) {
            $widgetCategoryType = $this->getRequest()->getParam('widgetCategoryType');
        }
        $widgetTypeList = $this->_getWidgetTypeList($widgetType);
        
        $categoryWidgets = \KC\Repository\PageWidgets::getAllWidgetsByType($widgetType, $widgetCategoryType);
        $widgetsIds = array();
        if (!empty($categoryWidgets)) {
            $widgetsIds = self::getWidgetIds($categoryWidgets);
        }
        $widgetsList = \KC\Repository\Widget::getUserDefinedWidgetList($widgetsIds);
        $this->view->widgetCategories = $widgetCategories;
        $this->view->widgetTypeList = $widgetTypeList;
        $this->view->widgetsList = $widgetsList;
        $this->view->widgetType = $widgetType;
        $this->view->widgetCategoryType = $widgetCategoryType;
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
        $widgetCategoryTypeId = $this->getRequest()->getParam('widgetCategoryType');
        $savedStatus = \KC\Repository\PageWidgets::addWidgetInList($widgetId, $widgetType, $widgetCategoryTypeId);
        echo Zend_Json::encode($savedStatus);
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
