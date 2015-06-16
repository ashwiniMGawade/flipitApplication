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
            $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function getcontactformdetailsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        require_once LIBRARY_PATH. "/recaptchalib.php";
        if ($this->getRequest()->isPost()) {
            $parameters = $this->_getAllParams();
            $visitorName = FrontEnd_Helper_viewHelper::sanitize($parameters['name']);
            $visitorEmail = FrontEnd_Helper_viewHelper::sanitize($parameters['email']);
            $subject = FrontEnd_Helper_viewHelper::sanitize($parameters['subject']);
            $message = FrontEnd_Helper_viewHelper::sanitize($parameters['message']);
            $captchaResponse = $parameters['g-recaptcha-response'];
            $reCaptcha = new ReCaptcha(FrontEnd_Helper_viewHelper::getCaptchaKey('captchaSecretKey'));
            $response = null;
            if ($captchaResponse) {
                $response = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"],
                    $captchaResponse
                );
            }
            if ($response != null && $response->success) {
                self::sendMailThroughMandril($visitorName, $visitorEmail, $subject, $message);
            } else {
                $errorMessage = FrontEnd_Helper_viewHelper::__translate("There is Issue in Captcha");
                $flashMessage = $this->_helper->getHelper('FlashMessenger');
                $flashMessage->addMessage(array('success' => $errorMessage));
                $urlToRedirect = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_info').'/'.
                    FrontEnd_Helper_viewHelper::__link('link_contact');
                $urlToRedirect = array('urlLocation' => $urlToRedirect);
                echo $this->_helper->json->sendJson($urlToRedirect);
            }
        }
    }

    public function sendMailThroughMandril($visitorName, $visitorEmail, $subject, $message)
    {
        $adminEmail = \KC\Repository\Signupmaxaccount::getEmailAddress();
        if (!empty($visitorEmail)) {
            $mailer  = new \FrontEnd_Helper_Mailer();
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
                \FrontEnd_Helper_viewHelper::__email('email_sitename'),
                $adminEmail[0]['emailperlocale'],
                \FrontEnd_Helper_viewHelper::__email('email_'.$subject),
                $content,
                \FrontEnd_Helper_viewHelper::__email('email_Contact header'),
                '',
                $this->_loginLinkAndData
            );
        }
        $successMessage = "Your message has been sent.";
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array('success' => $successMessage));
        $urlToRedirect = HTTP_PATH_LOCALE.\FrontEnd_Helper_viewHelper::__link('link_info').'/'.
            \FrontEnd_Helper_viewHelper::__link('link_contact');
        $urlToRedirect = array('urlLocation' => $urlToRedirect);
        echo $this->_helper->json->sendJson($urlToRedirect);
    }
}
