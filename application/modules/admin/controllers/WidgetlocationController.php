<?php
class Admin_WidgetlocationController extends Zend_Controller_Action
{
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
    }

    public function saveOrUpdateWidgetLocationAction()
    {
        $parameters = $this->getRequest()->getParams();
        $saveWidgetLocationStatus = false;
        if (!empty($parameters['widgetPostion']) && !empty($parameters['widgetLocation'])) {
            KC\Repository\WidgetLocation::saveOrUpdateWidgetLocation($parameters);
            $saveWidgetLocationStatus = true;
        }
        echo \Zend_Json::encode($saveWidgetLocationStatus);
        die();
    }
}
