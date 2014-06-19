<?php
class FrontEnd_Helper_Mailer {

    private $mandrill;
    private $_view;

    public function __construct()
    {
        $mandrillKey    =  Zend_Controller_Front::getInstance()->getParam('mandrillKey');
        $this->mandrill = new Mandrill_Init('_99EQUbVJHnKffb_ImwIUQ');

        //$this->_view    = Zend_Layout::getMvcInstance()->getView();
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
        $directlinks = '',
        $footerContent = '',
        $pathConstants
    ) {
        
  /*  echo "<pre>fromName-"; print_r($fromName); echo "</pre>";
    echo "<pre>fromEmail-"; print_r($fromEmail); echo "</pre>";
    echo "<pre>visitorName-"; print_r($visitorName); echo "</pre>";
    echo "<pre>visitorEmail-"; print_r($visitorEmail); echo "</pre>";
    echo "<pre>subject-"; print_r($subject); echo "</pre>";
    echo "<pre>content-"; print_r($content); echo "</pre>";
    echo "<pre>headerText-"; print_r($headerText); echo "</pre>";
    echo "<pre>recipientMetaData"; print_r($recipientMetaData); echo "</pre>";
    echo "<pre>directlinks"; print_r($directlinks); echo "</pre>";*/

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
            $directLoginAndUnsubscribeLinks = $directlinks;
        } else {
            $directLoginAndUnsubscribeLinks = '';
        } 

        if (!empty($pathConstants)) {
            $siteUrl = $pathConstants['httpPathLocale'];
        } else {
            $siteUrl = HTTP_PATH_LOCALE;
        }
        $message = array(
                        'subject'    => $subject,
                        'from_email' => $fromEmail,
                        'from_name'  => $fromName,
                        'to'         => $to,
                        'inline_css' => true,
                        "recipient_metadata" => $recipientMetaData,
                        'merge_vars' => $directLoginAndUnsubscribeLinks
                    );

        $emailHeader = array(
                        'name'    => 'header',
                        'content' => $headerText
                        );
         $basePath = new Zend_View();
        $basePath->setBasePath(APPLICATION_PATH . '/views/');
        $footer = array(
                        'name'    => 'footer',
                        'content' =>  $footerContent,
                        array(
                            'siteUrl' => $siteUrl
                        )
                    );

        $result = $this->mandrill->messages->sendTemplate('main', array($content, $footer, $emailHeader), $message);
    }

    public static function getDirectLoginAndUnsubscribeLinks($visitorEmail)
    {
        if (is_array($visitorEmail)) {
            $directLoginLink = array();
            $directUnsubscribeLink = array();
            foreach ($visitorEmail as $links) {
                $links = $links['vars'];
                $directLoginLink[] = $links[0]['content'];
                $directUnsubscribeLink[] = $links[1]['content'];
            }
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
