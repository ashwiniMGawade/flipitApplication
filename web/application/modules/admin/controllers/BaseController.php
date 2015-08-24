<?php

class Application_Admin_BaseController extends Zend_Controller_Action
{
    public function setFlashMessage($messageType, $message)
    {
        $singleMessage = '';
        if (is_array($message)) {
            foreach ($message as $fieldMessage) {
                $singleMessage .= implode('<br>', $fieldMessage) . '<br>';
            }
        } else {
            $singleMessage = $message;
        }
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $singleMessage = $this->view->translate($singleMessage);
        $flashMessenger->addMessage(array($messageType => $singleMessage));
    }
}
