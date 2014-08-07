<?php
class Admin_RobotController extends Zend_Controller_Action
{
    public $flashMessenger = '';

    public function preDispatch()
    {
        $databaseConnection = BackEnd_Helper_viewHelper::addConnection();

        if (!Auth_StaffAdapter::hasIdentity()) {
            $pageReferer = new Zend_Session_Namespace('referer');
            $pageReferer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        BackEnd_Helper_viewHelper::closeConnection($databaseConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();

        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1'
            && $sessionNamespace->settings['rights']['administration']['rights'] !='2') {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('You have no permission to access page');
            $flashMessenger->addMessage(array('error' => $message));
            $this->_redirect('/admin');
        }
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->robotObject = new Robot();
    }

    public function indexAction()
    {
        $this->getFlashMessage();
    }

    public function getrobotfilecontentAction()
    {   $this->_helper->layout->disableLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $websiteId = intval($this->getRequest()->getParam('websiteId', false));
            if ($websiteId) {
                $fileContent = $this->robotObject->getRobotTextFileInformation($websiteId);
                echo json_encode($fileContent[0]['content']);
                exit();
            }
            
        }
    }

    public function updaterobotcontentAction()
    {
        $robotFileParameters = $this->_getAllParams();
        $updateFileContent = $this->robotObject->updateFileInformation(
            $robotFileParameters['website'],
            $robotFileParameters['content']
        );
        if ($updateFileContent) {
            $pathToFile = $robotFileParameters['website'] == 1 ? $_SERVER['DOCUMENT_ROOT'] . '/public/flipit/' : $_SERVER['DOCUMENT_ROOT'] . '/public/';
            $robotTextFile = $pathToFile."robots.txt";
            $robotTextHandle = fopen($robotTextFile, 'w');
            fwrite($robotTextHandle, $robotFileParameters['content']);
            fclose($robotTextHandle);
            $this->setFlashMessage('Robot.txt has been updated!!!');
            $this->_redirect(HTTP_PATH . 'admin/robot');
        }
    }

    public function getFlashMessage()
    {
        $message = $this->flashMessenger->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
        return $this;
    }

    public function setFlashMessage($messageText)
    {
        $message = $this->view->translate($messageText);
        $this->flashMessenger->addMessage(array('success' => $message));
        return $this;
    }
}
