<?php

class Admin_ApikeysController extends Zend_Controller_Action
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
            $this->_redirect('/admin/auth/index');
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

    public function getapikeysAction()
    {
        $params = $this->_getAllParams();
        $Ipaddresses = \KC\Repository\IpAddresses::getAllIpaddresses($params);
        echo Zend_Json::encode($Ipaddresses);
        die();
    }

    public function addapikeysAction()
    {
        $params = $this->_getAllParams();
        $apiKeysForm = new Application_Form_ApiKeys();
        $this->view->form = $apiKeysForm;
        if ($this->getRequest()->isPost()) {
            if ($apiKeysForm->isValid($this->getRequest()->getPost())) {
                $savedApiKey = KC\Repository\IpAddresses::addIpaddress($params);
                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('API Key has been created successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH . 'admin/apikeys');
            } else {
                $apiKeysForm->highlightErrorElements();
            }
        }

    }

    public function editipaddressAction()
    {
        $ipAddressId = $this->getRequest()->getParam('id');
        $qstring = $_SERVER['QUERY_STRING'];
        $apiKeysForm = new Application_Form_IpAddress();
        $apiKeysForm->getElement('id')->setValue($ipAddressId);
        $apiKeysForm->getElement('qString')->setValue($qstring);
        $this->view->form = $apiKeysForm;
        if ($ipAddressId > 0) {
            $ipAddressForEdit = \KC\Repository\IpAddresses::getIpaddressForEdit($ipAddressId);
            $apiKeysForm->getElement('name')->setValue($ipAddressForEdit[0]['name']);
            $apiKeysForm->getElement('ipaddress')->setValue($ipAddressForEdit[0]['ipaddress']);
        }
        if ($this->getRequest()->isPost()) {
            if ($apiKeysForm->isValid($this->getRequest()->getPost())) {
                $params = $this->getRequest()->getParams();
                $ipAddressForEdit = \KC\Repository\IpAddresses::addIpaddress($params);
                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('IP address has been updated successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH.'admin/ipaddresses#'.$params['qString']);
            } else {
                $apiKeysForm->highlightErrorElements();
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
