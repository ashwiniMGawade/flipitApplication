<?php
class Admin_RobotController extends Application_Admin_BaseController
{
    public $flashMessenger = '';

    public function preDispatch()
    {
        $databaseConnection = \BackEnd_Helper_viewHelper::addConnection();

        if (!\Auth_StaffAdapter::hasIdentity()) {
            $pageReferer = new Zend_Session_Namespace('referer');
            $pageReferer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        \BackEnd_Helper_viewHelper::closeConnection($databaseConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();

        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('You have no permission to access page');
            $flashMessenger->addMessage(array('error' => $message));
            $this->_redirect('/admin');
        }
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->robotObject = new \KC\Repository\Robot();
    }

    public function indexAction()
    {
        $this->getFlashMessage();
    }

    public function getrobotfilecontentAction()
    {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $websiteId = intval($this->getRequest()->getParam('websiteId', false));
            if ($websiteId) {
                $robotsFileContent = $this->robotObject->getRobotTextFileInformation($websiteId);
                if (!empty($robotsFileContent)) {
                    echo json_encode($robotsFileContent[0]['content']);
                } else {
                    echo '';
                }
                exit();
            }
        }
    }

    public function updaterobotcontentAction()
    {
        $robotsFileParameters = $this->_getAllParams();
        $updateFileContent = $this->robotObject->updateFileInformation(
            $robotsFileParameters['website'],
            $robotsFileParameters['content']
        );
        if ($updateFileContent) {
            $pathToFile = $robotsFileParameters['website'] == 1 ? $_SERVER['DOCUMENT_ROOT'] . '/public/flipit/'
            : $_SERVER['DOCUMENT_ROOT'] . '/public/';
            $robotsTextFile = $pathToFile."robots.txt";
            $robotsTextHandle = fopen($robotsTextFile, 'w');
            fwrite($robotsTextHandle, $robotsFileParameters['content']);
            fclose($robotsTextHandle);
            $this->setSingleFlashMessage('Robot.txt has been updated!!!');
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

    public function setSingleFlashMessage($messageText)
    {
        $message = $this->view->translate($messageText);
        $this->flashMessenger->addMessage(array('success' => $message));
        return $this;
    }
}
