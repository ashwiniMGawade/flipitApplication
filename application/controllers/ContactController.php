<?php

class ContactController extends Zend_Controller_Action
{
    public $_loginLinkAndData = array();
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH. '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
            $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function getcontactformdetailsAction()
    {
        $parameters = $this->_getAllParams();
        $visitorName = $parameters['name'];
        $visitorEmail = FrontEnd_Helper_viewHelper::sanitize($parameters['email']);
        $subject = $parameters['subject'];
        $message = $parameters['message'];
        self::sendMailThroughMandril($visitorName, $visitorEmail, $subject, $message);
    }

    public function sendMailThroughMandril($visitorName, $visitorEmail, $subject, $message)
    {
        $adminEmail = Signupmaxaccount::getEmailAddress();
        if (!empty($visitorEmail)) {
            $mailer  = new FrontEnd_Helper_Mailer();
            $content = array(
                'name'    => 'content',
                'content' => $this->view->partial(
                    'emails/contactform.phtml',
                    array(
                        'message' => $message,
                        'name' => $visitorName,
                        'email'=> $visitorEmail,
                        'subject'=>$subject
                    )
                )
            );
            $mailer->send(
                $visitorName,
                $visitorEmail,
                FrontEnd_Helper_viewHelper::__email('email_sitename'),
                $adminEmail[0]['emailperlocale'],
                FrontEnd_Helper_viewHelper::__email('email_'.$subject),
                $content,
                FrontEnd_Helper_viewHelper::__email('email_Contact header'),
                '',
                $this->_loginLinkAndData
            );
        }
        $successMessage = "Your message has been sent.";
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array('success' => $successMessage));
        $urlToRedirect = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_info').'/'.
            FrontEnd_Helper_viewHelper::__link('link_contact');
        $this->_redirect($urlToRedirect);
    }
}
