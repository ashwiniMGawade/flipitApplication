<?php

class Admin_SpecialPagesOffersController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $dbConnection = BackEnd_Helper_viewHelper::addConnection(); // connection
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        
    }

    public function savepopulararticlespositionAction()
    {
        return true;
    }
}
