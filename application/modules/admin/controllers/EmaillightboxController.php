<?php

class Admin_EmaillightboxController extends Zend_Controller_Action
{

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
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

    }

    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
        // get flashes
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset( $message [0] ['success']) ? $message [0] ['success'] : '';
        $this->view->messageError = isset($message [0] ['error']) ? $message [0] ['error'] : '';
        // check if form is submitted
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            // call function to update email lightbox
            $Data = \KC\Repository\EmailLightBox::update($params);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Email Lightbox has been updated successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect(HTTP_PATH . 'admin/emaillightbox');
        }

        //return updated email lightbox content
        $this->view->retData = \KC\Repository\EmailLightBox::getLigthBoxContent();
        //echo "<pre>";
        //print_r($this->view->retData);die;
    }

    public function emaillightboxstatusAction()
    {
        $params = $this->_getAllParams();
        // call function to change email lightbox status
        \KC\Repository\EmailLightBox::changeStatus($params);
        die ();
    }



}
