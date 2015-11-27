<?php

class Admin_IpaddressesController extends Application_Admin_BaseController
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
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $this->_redirect('/admin');
        }
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
        $Ipaddresses = \KC\Repository\IpAddresses::getAllIpaddresses($params);
        echo Zend_Json::encode($Ipaddresses);
        die();
    }

    public function addipaddressAction()
    {
        $params = $this->_getAllParams();
        $ipAddressForm = new Application_Form_IpAddress();
        $this->view->form = $ipAddressForm;
        if ($this->getRequest()->isPost()) {
            if ($ipAddressForm->isValid($this->getRequest()->getPost())) {
                $savedIpAddress = KC\Repository\IpAddresses::addIpaddress($params);
                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('IP address has been added successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH . 'admin/ipaddresses');
            } else {
                $ipAddressForm->highlightErrorElements();
            }
        }

    }

    public function editipaddressAction()
    {
        $ipAddressId = $this->getRequest()->getParam('id');
        $qstring = $_SERVER['QUERY_STRING'];
        $ipAddressForm = new Application_Form_IpAddress();
        $ipAddressForm->getElement('id')->setValue($ipAddressId);
        $ipAddressForm->getElement('qString')->setValue($qstring);
        $this->view->form = $ipAddressForm;
        if ($ipAddressId > 0) {
            $ipAddressForEdit = \KC\Repository\IpAddresses::getIpaddressForEdit($ipAddressId);
            $ipAddressForm->getElement('name')->setValue($ipAddressForEdit[0]['name']);
            $ipAddressForm->getElement('ipaddress')->setValue($ipAddressForEdit[0]['ipaddress']);
        }
        if ($this->getRequest()->isPost()) {
            if ($ipAddressForm->isValid($this->getRequest()->getPost())) {
                $params = $this->getRequest()->getParams();
                $ipAddressForEdit = \KC\Repository\IpAddresses::addIpaddress($params);
                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('IP address has been updated successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH.'admin/ipaddresses#'.$params['qString']);
            } else {
                $ipAddressForm->highlightErrorElements();
            }
        }
    }

    public function deleteipaddressAction()
    {
        $ipAddressId = $this->getRequest()->getParam('id');
        \KC\Repository\IpAddresses::deleteIpaddress($ipAddressId);
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('IP address has been deleted successfully');
        $flash->addMessage(array('success' => $message));
        exit();
    }
}
