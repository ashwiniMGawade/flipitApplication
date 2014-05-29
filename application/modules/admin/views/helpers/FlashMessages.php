<?php
class Zend_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    // TODO - Insert your code here

    public function __construct()
    {
        // TODO - Insert your code here
    }


    public function flashMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (!empty($messages)) {
            $output .= '<ul class="flashMeessages">';
            foreach ($messages as $message) {
                $output .= '<li class="' . key($message) . '">' . current($message) . '</li>';
            }
            $output .= '</ul>';
        }

        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        $flash->clearCurrentMessages();

        return $output;
    }


}
