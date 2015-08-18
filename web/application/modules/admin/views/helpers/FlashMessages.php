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
        if(empty($messages)) {
            $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getCurrentMessages();
        }

        $output = '';
        if (isset($messages[0]) && !empty($messages[0])) {
            $output .= '<br><div class ="mainpage-content-colorbox success">';
            foreach ($messages[0] as $message_type => $message) {
                $output .= '<span class="' . $message_type . 'server">' . $message . '</span>';
            }
            $output .= '</div>';
        }

        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        $flash->clearCurrentMessages();

        return $output;
    }


}
