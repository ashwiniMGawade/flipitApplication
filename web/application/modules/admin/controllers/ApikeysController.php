<?php
use Core\Domain\Factory\AdministratorFactory;

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
        $apiKeys = AdministratorFactory::getApiKeys()->execute();
        $apiKeys = $this->toArray($apiKeys);
        echo Zend_Json::encode($apiKeys);
        exit;
    }

    public function addapikeyAction()
    {
        $user = Auth_StaffAdapter::getIdentity();
        $entityValues = array(
            'api_key' => AdministratorFactory::apiKey()->generate(),
            'user' => $user,
            'created_at' => new \DateTime(),
            'deleted' => 0
        );
        AdministratorFactory::createApiKey()->execute($entityValues);
        exit;
    }

    private function toArray($apiKeys)
    {
        $aaData = array();
        foreach ($apiKeys as $apiKey) {
            $aaData[] = array(
                'id' => $apiKey->__get('id'),
                'apiKey' => $apiKey->__get('api_key'),
                'createdAt' => $apiKey->__get('created_at')
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
