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

    public function send(
        $fromName,
        $fromEmail,
        $visitorName,
        $visitorEmail,
        $subject,
        $content,
        $headerText,
        $recipientMetaData = '',
        $directlinks = ''
    ) {
        
    echo "<pre>fromName-"; print_r($fromName); echo "</pre>";
    echo "<pre>fromEmail-"; print_r($fromEmail); echo "</pre>";
    echo "<pre>visitorName-"; print_r($visitorName); echo "</pre>";
    echo "<pre>visitorEmail-"; print_r($visitorEmail); echo "</pre>";
    echo "<pre>subject-"; print_r($subject); echo "</pre>";
    echo "<pre>content-"; print_r($content); echo "</pre>";
    echo "<pre>headerText-"; print_r($headerText); echo "</pre>";
    echo "<pre>recipientMetaData"; print_r($recipientMetaData); echo "</pre>";
    echo "<pre>directlinks"; print_r($directlinks); echo "</pre>";

        if (is_array($visitorEmail)) {
            $to = $visitorEmail;
        } else {
            $to = array(
                array(
                    'email'=> $visitorEmail,
                    'name'=> $visitorName
                )
            );
        }

        if (!empty($directlinks)) {
            $directLoginAndUnsubscribeLinks = self::getDirectLoginAndUnsubscribeLinks($directlinks);
        } else {
            $directLoginAndUnsubscribeLinks = self::getDirectLoginAndUnsubscribeLinks($visitorEmail);
        }  die;
        $message = array(
                        'subject'    => $subject,
                        'from_email' => $fromEmail,
                        'from_name'  => $fromName,
                        'to'         => $to,
                        'inline_css' => true,
                        "recipient_metadata" => $recipientMetaData,
                    );

        $emailHeader = array(
                        'name'    => 'header',
                        'content' => $headerText
                        );
         $basePath = new Zend_View();
        $basePath->setBasePath(APPLICATION_PATH . '/views/');
        $footer = array(
                        'name'    => 'footer',
                        'content' =>  $basePath->partial(
                            'emails/footer.phtml',
                            array(
                                'directLoginAndUnsubscribeLinks' =>
                                    $directLoginAndUnsubscribeLinks
                            )
                        ),
                        array(
                            'siteUrl' => HTTP_PATH_LOCALE
                        )
                    );

        $result = $this->mandrill->messages->sendTemplate('main', array($content, $footer, $emailHeader), $message);
    }

    public static function getDirectLoginAndUnsubscribeLinks($visitorEmail)
    {
        if (is_array($visitorEmail)) {
            foreach ($visitorEmail as $links) {
                echo "<pre>"; print_r($links['vars']); echo "</pre>";
            }
            $directLoginLink = $visitorEmail[0]['content'];
            $directUnsubscribeLink = $visitorEmail[1]['content'];
        } else {
            $visitorDetails = Visitor::getVisitorDetailsByEmail($visitorEmail);
            $directLoginLink = HTTP_PATH_LOCALE .
                FrontEnd_Helper_viewHelper::__link("link_login") ."/" .
                FrontEnd_Helper_viewHelper::__link("link_directlogin") . "/" .
                base64_encode($visitorEmail) ."/". $visitorDetails[0]['password'];
            $directUnsubscribeLink = HTTP_PATH_LOCALE .
                FrontEnd_Helper_viewHelper::__link("link_login") . "/" .
                FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe") . "/" .
                base64_encode($visitorEmail) ."/". $visitorDetails[0]['password'];
        }
        return array('directLoginLink' => $directLoginLink, 'directUnsubscribeLink' => $directUnsubscribeLink);
    }
}
