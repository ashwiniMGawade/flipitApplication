<?php
class FrontEnd_Helper_MandrillMailFunctions extends FrontEnd_Helper_viewHelper {
    public function sendForgotPasswordMail($visitorId, $emailAddress, $currentController) {
        $imageLogoForMail = "<a href=".HTTP_PATH_LOCALE.">
        <img alt='flipit-welcome' src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/>
        </a>";
        $siteName = "Flipit.com";
        $siteUrl = HTTP_PATH_LOCALE;
        if (HTTP_HOST == "www.kortingscode.nl") {
            $imageLogoForMail = "<a href=".HTTP_PATH_LOCALE."><img alt='HeaderMail' src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";
            $siteName = "Kortingscode.nl";
        }
        $mailData = array(
                array('name'=> 'headerWelcome',
                        'content'=>$imageLogoForMail),
                array('name'=>'bestRegards',
                        'content'=>$this->zendTranslate->translate('Best').' '.$siteName.' '.$this->zendTranslate->translate('visitor,')
                ),
                array('name'=> 'centerContent',
                        'content'=>
                        $this->zendTranslate->translate('No problem you have forgotten your password, use the following link you can set it up again:').
                        '<a href="'
                        . HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'
                        .FrontEnd_Helper_viewHelper::__link('resetpassword').'/'
                        .base64_encode($visitorId)
                        . '">'.$this->zendTranslate->translate('Click Here').'</a>'
                ),
                array('name'=> 'bottomContent',
                        'content'=> $this->zendTranslate->translate('Greetings').',<br><br>'
                        . $this->zendTranslate->translate('The editors of Kortingscode.nl')
                ),
                array('name'=> 'copyright',
                        'content'=>$this->zendTranslate->translate('Copyright &copy; 2013').' '.$siteName.'. '
                        .$this->zendTranslate->translate('All Rights Reserved.')
                ),
                array('name'=> 'address',
                        'content'=>$this->zendTranslate->translate("You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                        '<br/>' . $this->zendTranslate->translate("Offers.")
                        . '<a href='.$siteUrl.' style="color:#ffffff; padding:0 2px;">'.$siteName.'</a>'
                        . $this->zendTranslate->translate('is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
                ),
                array('name'=> 'logincontact',
                        'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;"
                        href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'">'
                        .$this->zendTranslate->translate('login').'</a>'
                ));
        
        $emailData = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $emailData[0]['emailperlocale'];
        $mandrill = new Mandrill_Init($currentController->getInvokeArg('mandrillKey'));
        $templateName = $currentController->getInvokeArg('welcomeTemplate');
        $templateContent = $mailData;
        $message = array(
                'subject'    => $this->zendTranslate->translate('Password Change'),
                'from_email' => $emailFrom,
                'from_name'  => $this->zendTranslate->translate('Forgot-Password'),
                'to'         => array(array('email'=>$emailAddress, 'name'=> 'Member')) ,
                'inline_css' => true
        );
        $mandrill->messages->sendTemplate($templateName, $templateContent, $message);
        Visitor::updatePasswordRequest($visitorId, 0);
        return true;
    }
}
