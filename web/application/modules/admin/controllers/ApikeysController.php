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
        $apiKeys = AdminFactory::getApiKeys()->execute();
        $apiKeys = $this->prepareData($apiKeys);
        echo Zend_Json::encode($apiKeys);
        exit;
    }

    public function createAction()
    {
        $user = Auth_StaffAdapter::getIdentity();
        $apiKey = AdminFactory::createApiKey()->execute();
        try {
            AdminFactory::addApiKey()->execute($apiKey, $user);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Api Key has been successfully Added');
            $flash->addMessage(array('success' => $message));
        } catch (Exception $exception) {
            $this->createAction();
        }
        exit;
    }

    public function deleteAction()
    {
        $apiKeyId = $this->getRequest()->getParam('id');
        AdminFactory::deleteApiKey()->execute(FrontEnd_Helper_viewHelper::sanitize($apiKeyId));
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('Api Key has been deleted successfully');
        $flash->addMessage(array('success' => $message));
        exit();
    }

    private function prepareData($apiKeys)
    {
        $aaData = array();
        foreach ($apiKeys as $apiKey) {
            $aaData[] = array(
                'id' => $apiKey->getId(),
                'apiKey' => $apiKey->getApiKey(),
                'createdAt' => $apiKey->getCreatedAt()
            );
        }
        $returnData = array(
            "sEcho" => "1",
            "aaData" => $aaData,
            "iTotalRecords" => count($aaData),
            "iTotalDisplayRecords" => 20
        );
        return $returnData;
    }
}
