<?php

class Application_Admin_BaseController extends Zend_Controller_Action
{
    public function setFlashMessage($messageType, $message)
    {
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate($message);
        $flashMessenger->addMessage(array($messageType => $message));
    }
}