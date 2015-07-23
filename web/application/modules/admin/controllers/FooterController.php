<?php

class Admin_FooterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }

    public function indexAction()
    {
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';


        // check if form is submitted
        if ($this->_request->isPost()) {
            $parmas = $this->_getAllParams();

            KC\Repository\Footer::update($parmas);
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Footer has been updated successfully' );
            $flash->addMessage ( array ('success' => $message ) );
            $this->_redirect ( HTTP_PATH . 'admin/footer' );

        }

        // return updated footer content
        $this->view->footer = KC\Repository\Footer::getFooterContent();
    }

    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()
        ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

        $sessionNamespace = new \Zend_Session_Namespace();
        if($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate ( 'You have no permission to access page' );
            $flash->addMessage ( array ('error' => $message ));
            $this->_redirect ( '/admin' );
        }
    }

}
