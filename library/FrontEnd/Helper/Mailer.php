<?php
class FrontEnd_Helper_Mailer {

    private $mandrill;
    private $_view;

    public function __construct()
    {
        $mandrillKey    =  Zend_Controller_Front::getInstance()->getParam('mandrillKey');
        $this->mandrill = new Mandrill_Init($mandrillKey);

        $this->_view    = Zend_Layout::getMvcInstance()->getView();
    }

    public function send($fromName, $fromEmail, $visitorName, $visitorEmail, $subject, $content, $headerText)
    {
        $message = array(
                        'subject'    => $subject,
                        'from_email' => $fromEmail,
                        'from_name'  => $fromName,
                        'to'         => array(array('email'=> $visitorEmail, 'name'=> $visitorName)),
                        'inline_css' => true
                    );

        $emailHeader = array(
                        'name'    => 'header',
                        'content' => $headerText
                        );
        $footer = array(
                        'name'    => 'footer',
                        'content' => $this->_view->partial('emails/footer.phtml'),
                            array(
                                'siteUrl' => HTTP_PATH_LOCALE
                            )
                );

        $result = $this->mandrill->messages->sendTemplate('main', array($content, $footer, $emailHeader), $message);
    }
}
