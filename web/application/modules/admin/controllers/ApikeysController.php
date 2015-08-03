<?php
use \Core\Domain\Factory\AdminFactory;

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

    public function getAction()
    {
        $sEcho = 1;
        $apiKeys = AdminFactory::getApiKeys()->execute();
        $apiKeys = $this->prepareData($apiKeys);
        $response = \DataTable_Helper::createResponse($sEcho, $apiKeys, count($apiKeys));
        echo Zend_Json::encode($response);
        exit;
    }

    public function createAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $user = Auth_StaffAdapter::getIdentity();
        $apiKey = AdminFactory::createApiKey()->execute();
        try {
            AdminFactory::addApiKey()->execute($apiKey, $user);
            $message = $this->view->translate('Api Key has been successfully added');
            $flash->addMessage(array('success' => $message));
        } catch (Exception $exception) {
            $message = $this->view->translate($exception->getMessage());
            $flash->addMessage(array('error' => $message));
        }
        exit;
    }

    public function deleteAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $apiKeyId = $this->getRequest()->getParam('id');
        if (!$apiKeyId) {
            $message = $this->view->translate('Invalid Id');
            $flash->addMessage(array('error' => $message));
        }
        AdminFactory::deleteApiKey()->execute((int) FrontEnd_Helper_viewHelper::sanitize($apiKeyId));
        $message = $this->view->translate('Api Key has been deleted successfully');
        $flash->addMessage(array('success' => $message));
        exit();
    }

    private function prepareData($apiKeys)
    {
        $returnData = array();
        foreach ($apiKeys as $apiKey) {
            $returnData[] = array(
                'id' => $apiKey->getId(),
                'apiKey' => $apiKey->getApiKey(),
                'createdAt' => $apiKey->getCreatedAt()
            );
        }
        return $returnData;
    }
}
