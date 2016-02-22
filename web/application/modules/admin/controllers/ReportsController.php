<?php

use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class Admin_ReportsController extends Application_Admin_BaseController
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

    public function shopsAction()
    {

    }
}
