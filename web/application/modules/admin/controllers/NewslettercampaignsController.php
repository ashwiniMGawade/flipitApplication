<?php

class Admin_NewslettercampaignsController extends Application_Admin_BaseController
{

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()
            ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if (
            $sessionNamespace->settings['rights']['content']['rights'] != '1'
            && $sessionNamespace->settings['rights']['content']['rights'] != '2'
        ) {
            $this->_redirect('/admin/auth/index');
        }
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }

    public function createAction()
    {

    }

    public function settingsAction()
    {

    }

}
