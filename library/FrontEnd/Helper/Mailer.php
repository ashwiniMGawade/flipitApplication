<?php
class FrontEnd_Helper_Mailer {

    private $mandrill;
    private $_view;

    public function __construct($pathConstants = '')
    {
        if (!empty($pathConstants)) {
            $mandrillKey = $pathConstants['mandrillKey'];
        } else {
            $mandrillKey = Zend_Controller_Front::getInstance()->getParam('mandrillKey');
        }
        $this->mandrill = new Mandrill_Init($mandrillKey);
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
        $pathConstants = '',
        $newsLetterFooterImage = ''
    ) {
        $to = is_array($visitorEmail) ? $visitorEmail : array(
                array(
                    'email'=> $visitorEmail,
                    'name'=> $visitorName
                )
            );
        $directLoginAndUnsubscribeLinks = !empty($directlinks) ? $directlinks : '';
        if (!empty($pathConstants)) {
            if (!isset($pathConstants['exportScript'])) {
                $email = Settings::getEmailSettings('sender_email_address');
                $name = Settings::getEmailSettings('sender_name');
                $fromEmail = !empty($email) ? $email : $fromEmail;
                $fromName = !empty($name) ? $name :  $fromName;
            }
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

        if (!empty($pathConstants)) {
            $siteUrl = $pathConstants['httpPathLocale'];
            $httpPath = $pathConstants['httpPath'].'/';
            $locale = $pathConstants['locale'];
            $publicPathCdn = $pathConstants['publicPathCdn'];
        } else {
            $siteUrl = LOCALE != '' ? 'http://www.flipit.com/'.LOCALE.'/' : 'http://www.kortingscode.nl/';
            $httpPath = LOCALE != '' ? 'http://www.flipit.com/' : 'http://www.kortingscode.nl/';
            $locale = LOCALE;
            $publicPathCdn = LOCALE != '' ? 'http://img.flipit.com/public/'.LOCALE.'/' : 'http://img.kortingscode.nl/public/';
        }

        $footer = array(
            'name'    => 'footer',
            'content' =>  '',
            array(
                'siteUrl' => ''
            )
        );
        
        if (!isset($pathConstants['exportScript'])) {
            $basePath = new Zend_View();
            $basePath->setBasePath(APPLICATION_PATH . '/views/');
            $footer = array(
                'name'    => 'footer',
                'content' =>  $basePath->partial(
                    'emails/footer.phtml',
                    array(
                        'httpPathLocale' => $siteUrl,
                        'httpPath' => $httpPath,
                        'locale' => $locale,
                        'newsLetterFooterImage' => $newsLetterFooterImage,
                        'publicPathCdn' => $publicPathCdn
                    )
                ),
                array(
                    'siteUrl' => $siteUrl
                )
            );
        }
        echo "<pre>";
        print_r($content);
        die;
        $result = $this->mandrill->messages->sendTemplate('main', array($content, $footer, $emailHeader), $message);
    }
}
