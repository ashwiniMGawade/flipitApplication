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

    public function send($fromName, $fromEmail, $subject, $content)
    {
        $message = array(
                        'subject'    => $subject,
                        'from_email' => $fromEmail,
                        'from_name'  => $fromName,
                        'to'         => array(array('email'=> $fromEmail, 'name'=> $fromName)),
                        'inline_css' => true
                    );

        $footer = array(
                        'name'    => 'footer',
                        'content' => $this->_view->partial('emails/footer.phtml', array('siteUrl' => 'Kortingscode.nl'))
                    );

        $result = $this->mandrill->messages->sendTemplate('main', array($content, $footer), $message);
        // echo '<pre>'.print_r($result, true).'</pre>';
    }
}
