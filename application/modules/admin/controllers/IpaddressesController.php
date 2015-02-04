<?php

class Admin_IpaddressesController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }

    public function init()
    {
        BackEnd_Helper_viewHelper::addConnection();
    }

    public function indexAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }

    public function getipaddresesAction()
    {
        $params = $this->_getAllParams();
        $Ipaddresses = Ipaddresses::getAllIpaddresses($params);
        echo Zend_Json::encode($Ipaddresses);
        die();
    }

    public function addipaddressAction()
    {
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            $keyword = Ipaddresses::addIpaddress($params);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('IP address has been added successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect(HTTP_PATH . 'admin/ipaddresses');
        }
    }

    public function editipaddressAction()
    {
        $ipAddressId = $this->getRequest()->getParam('id');
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if ($ipAddressId > 0) {
            $searchbar = Ipaddresses::getIpaddressForEdit($ipAddressId);
            $this->view->editIpaddress = $searchbar;
        }
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $searchbar = Ipaddresses::addIpaddress($params);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('IP address has been updated successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect(HTTP_PATH.'admin/ipaddresses#'.$params['qString']);
        }
    }

    public function deleteipaddressAction()
    {
        $ipAddressId = $this->getRequest()->getParam('id');
        Ipaddresses::deleteIpaddress($ipAddressId);
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('IP address has been deleted successfully');
        $flash->addMessage(array('success' => $message));
        exit();
    }
}
