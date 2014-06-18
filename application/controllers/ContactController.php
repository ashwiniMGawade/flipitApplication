<?php
# we set a passcache cookie for Flipit and Kortingscode.nl so that admin can bypass varnish
class ContactController extends Zend_Controller_Action
{

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
        $adminEmail = "asharma11@seasiainfotech.com";
        if (!empty($visitorEmail)) {
            $mailer  = new FrontEnd_Helper_Mailer();
            $content = array(
                'name'    => 'content',
                'content' => $this->view->partial(
                    'emails/contactform.phtml',
                    array(
                        'message' => $message
                    )
                )
            );
            $mailer->send(
                FrontEnd_Helper_viewHelper::__email('email_sitename'),
                $visitorEmail,
                $visitorName,
                $adminEmail,
                FrontEnd_Helper_viewHelper::__email('email_'.$subject),
                $content,
                FrontEnd_Helper_viewHelper::__email('email_Forgot password header')
            );
        }
        $successMessage = "Your message has been sent.";
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array('success' => $successMessage));
        $urlToRedirect = HTTP_PATH_LOCALE.'info/contact';
        $this->_redirect($urlToRedirect);
    }
}
