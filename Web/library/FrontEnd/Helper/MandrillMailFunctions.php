<?php
class FrontEnd_Helper_MandrillMailFunctions {
    public $imageLogoForMail = '';
    public $siteName = '';
    public function __construct() {
        $this->imageLogoForMail =
            "<a href=".HTTP_PATH_LOCALE.">
                <img alt='flipit-welcome' title='flipit-welcome' src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/>
            </a>";
        $this->siteName = "Flipit.com";
        if (HTTP_HOST == "www.kortingscode.nl") {
            $this->imageLogoForMail = 
                "<a href=".HTTP_PATH_LOCALE.">
                    <img alt='HeaderMail' title='HeaderMail' src='".HTTP_PATH."public/images/HeaderMail.gif'/>
                </a>";
            $this->siteName = "Kortingscode.nl";
        }
    }
    public function sendForgotPasswordMail($visitorId, $emailAddress, $currentController) {
        $mailData = array(
            array(
                'name'=> 'headerWelcome',
                'content'=>$this->imageLogoForMail),
            array('name'=>'bestRegards',
                'content'=>
                    FrontEnd_Helper_viewHelper::__email('email_Best').' '.$this->siteName.' '
                    .FrontEnd_Helper_viewHelper::__email('email_visitor,')
            ),
            array(
                'name'=> 'centerContent',
                'content'=>
                    FrontEnd_Helper_viewHelper::__email('email_No problem you have forgotten your password, use the following link you can set it up again:').
                    '<a href="'
                    . HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login').'/'
                    .FrontEnd_Helper_viewHelper::__link('link_resetpassword').'/'
                    .base64_encode($visitorId)
                    . '">'.FrontEnd_Helper_viewHelper::__email('email_Click Here').'</a>'
            ),
            array(
                'name'=> 'bottomContent',
                'content'=> FrontEnd_Helper_viewHelper::__email('email_Greetings').',<br><br>'
                    . FrontEnd_Helper_viewHelper::__email('email_The editors of Kortingscode.nl')
            ),
            array(
                'name'=> 'copyright',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_Copyright &copy; 2013').' '.$this->siteName.'. '
                    .FrontEnd_Helper_viewHelper::__email('email_All Rights Reserved.')
            ),
            array(
                'name'=> 'address',
                'content'=>FrontEnd_Helper_viewHelper::__email("email_You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                    '<br/>' . FrontEnd_Helper_viewHelper::__email("email_Offers.")
                    . '<a href='.HTTP_PATH_LOCALE.' style="color:#ffffff; padding:0 2px;">'.$this->siteName.'</a>'
                    . FrontEnd_Helper_viewHelper::__email('email_is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
            ),
            array(
                'name'=> 'logincontact',
                'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;"
                    href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login').'">'
                    .FrontEnd_Helper_viewHelper::__email('email_login').'</a>'
            )
        );
        $emailData = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $emailData[0]['emailperlocale'];
        self::sendTemplate(
            $currentController,
            'welcomeTemplate',
            $mailData,
            $emailFrom,
            FrontEnd_Helper_viewHelper::__email('email_Forgot-Password'),
            $emailAddress,
            FrontEnd_Helper_viewHelper::__email('email_Member'),
            FrontEnd_Helper_viewHelper::__email('email_Password Change')
        );
        Visitor::updatePasswordRequest($visitorId, 0);
        return true;
    }

    public function sendWelcomeMail($visitorId, $currentController)
    {
        $visitorDetails = Visitor::getUserDetails($visitorId);
        $voucherCodesData = $this->getTopVoucherCodesDataForMandrill(Offer::getTopOffers(5));
        $mailData = array(
            array(
                'name'=>'headerWelcome',
                'content'=>$this->imageLogoForMail
            ),
            array(
                'name'=>'bestRegards',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_Best news reader, ')
            ),
            array(
                'name'=>'centerContent',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_From now on you will receive our weekly newsletter with the best discount codes.')
            ),
            array(
                'name'=>'bottomContent',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_Thanks').', <br/>'.$this->siteName
            ),
            array(
                'name'=>'copyright',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_Copyright &copy; 2013').' '.$this->siteName.'. '
                    .FrontEnd_Helper_viewHelper::__email('email_All Rights Reserved.')
            ),
            array(
                'name'=>'address',
                'content'=>
                    FrontEnd_Helper_viewHelper::__email("email_You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                    '<br/>' . FrontEnd_Helper_viewHelper::__email("email_Offers.")
                    . '<a href='.HTTP_PATH_LOCALE.' style="color:#ffffff; padding:0 2px;">'.$this->siteName.'</a>' 
                    . FrontEnd_Helper_viewHelper::__email('email_is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
            ),
            array(
                'name'=>'logincontact',
                'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;" 
                    href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login').'/'
                    .FrontEnd_Helper_viewHelper::__link('link_directlogin'). "/" . base64_encode($visitorDetails[0]['email']) 
                    ."/". $visitorDetails[0]['password'].'">'.FrontEnd_Helper_viewHelper::__email('email_My Profile').'</a>'
            )
        );
        $staticContent = array(
            array(
                'name' => 'moreOffersLink',
                'content' => HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_top-20')
            ),
           array(
               'name' => 'moreOffers',
               'content' => FrontEnd_Helper_viewHelper::__email('email_See more of our top offers') . ' >'
           )
        );
        $poupularTitle = array(
            array(
                'name' => 'poupularTitle',
                'content' => FrontEnd_Helper_viewHelper::__email('email_Top 5 Discount Codes:')
            )
        );
        $mendrilMailData =
             array_merge(
                $voucherCodesData['dataShopName'],
                $voucherCodesData['dataOfferName'],
                $voucherCodesData['dataShopImage'],
                $voucherCodesData['expDate'],
                $mailData,
                $poupularTitle
             );
        $dataPermalink = array_merge($voucherCodesData['shopPermalink'], $staticContent);
        $emailData = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $emailData[0]['emailperlocale'];
        self::sendTemplate(
            $currentController,
            'welcomeTemplate',
            $mendrilMailData,
            $emailFrom,
            FrontEnd_Helper_viewHelper::__email('email_welcome'),
            $visitorDetails[0]['email'],
            !empty($visitorDetails[0]['firstName']) ? $visitorDetails[0]['firstName'] :FrontEnd_Helper_viewHelper::__email('email_Member'),
            FrontEnd_Helper_viewHelper::__email('email_Welcome e-mail subject')
        );
    }

    public function sendConfirmationMail($visitoremailMail, $currentController)
    {
        $mailData = array(
            array(
                'name'=> 'headerWelcome',
                'content'=>$this->imageLogoForMail),
            array('name'=>'bestRegards',
                'content'=>
                    FrontEnd_Helper_viewHelper::__email('email_Dear visitor Kortingscode.nl,')
            ),
            array(
                'name'=> 'centerContent',
                'content'=>
                    FrontEnd_Helper_viewHelper::__email('email_You are receiving this email because you have indicated to keep abreast of the best discount codes and actions Kortingscode.nl.:')
                   ."<br/>". FrontEnd_Helper_viewHelper::__email('email_Met de volgende stap bevestig je dat je deze e-mail hebt ontvangen, klik daarvoor op onderstaande link:').
                    '<a href="'
                    . HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login').'/'.FrontEnd_Helper_viewHelper::__link('link_confirmemail').'/'. base64_encode($visitoremailMail)
                    . '">'.FrontEnd_Helper_viewHelper::__email('email_Klik hier om je e-mail adres te bevestigen').'</a>'
            ),
            array(
                'name'=> 'bottomContent',
                'content'=> FrontEnd_Helper_viewHelper::__email('email_Thanks').',<br><br>'
                    . FrontEnd_Helper_viewHelper::__email('email_Kortingscode.nl')
            ),
            array(
                'name'=> 'copyright',
                'content'=>FrontEnd_Helper_viewHelper::__email('email_Copyright &copy; 2013').' '.$this->siteName.'. '
                    .FrontEnd_Helper_viewHelper::__email('email_All Rights Reserved.')
            ),
            array(
                'name'=> 'address',
                'content'=>FrontEnd_Helper_viewHelper::__email("email_You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                    '<br/>' . FrontEnd_Helper_viewHelper::__email("email_Offers.")
                    . '<a href='.HTTP_PATH_LOCALE.' style="color:#ffffff; padding:0 2px;">'.$this->siteName.'</a>'
                    . FrontEnd_Helper_viewHelper::__email('email_is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
            ),
            array(
                'name'=> 'logincontact',
                'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;"
                    href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login').'">'
                    .FrontEnd_Helper_viewHelper::__email('email_login').'</a>'
            )
        );
        $emailData = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $emailData[0]['emailperlocale'];
        self::sendTemplate(
                $currentController,
                'welcomeTemplate',
                $mailData,
                $emailFrom,
                FrontEnd_Helper_viewHelper::__email('email_Email confirmation'),
                $visitoremailMail,
                FrontEnd_Helper_viewHelper::__email('email_Member'),
                FrontEnd_Helper_viewHelper::__email('email_Email your confirmation')
        );
        return true;
    }
    public static function sendTemplate($currentController, $templateName, $mailData, $emailFrom, $fromName, $emailTo, $toName, $subject)
    {
        $mandrill = new Mandrill_Init($currentController->getInvokeArg('mandrillKey'));
        $templateName = $currentController->getInvokeArg($templateName);
        $templateContent = $mailData;
        $message =
        array(
            'subject'    => $subject,
            'from_email' => $emailFrom,
            'from_name'  => $fromName,
            'to'         => array(array('email'=>$emailTo, 'name'=> $toName)) ,
            'inline_css' => true
        );
        $mandrill->messages->sendTemplate($templateName, $templateContent, $message);
    }
    public static function getTopVoucherCodesDataForMandrill($topVouchercodes)
    {
       $dataShopName = $dataShopImage = $shopPermalink = $expireDate = $dataOfferName = array();
       foreach ($topVouchercodes as $offerIndex => $offer) {
          $permalinkEmail = 
              HTTP_PATH_LOCALE 
              . $offer['shop']['permalink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
          $dataShopName[$offerIndex]['name'] = "shopTitle_".($offerIndex+1);
          $dataShopName[$offerIndex]['content'] = 
              "<a style='color:#333333; text-decoration:none;'href='$permalinkEmail'>".$offer['shop']['name']."</a>";
          $dataOfferName[$offerIndex]['name'] = "offerTitle_".($offerIndex+1);
          $dataOfferName[$offerIndex]['content'] = $offer['title'];
          $dataShopLogo = 
              HTTP_PATH_CDN.$offer['shop']['logo']['path'].'thum_medium_store_'. $offer['shop']['logo']['name'];
          $dataShopImage[$offerIndex]['name'] = 'shopLogo_'.($offerIndex+1);
          $dataShopImage[$offerIndex]['content'] = "<a href='$permalinkEmail'><img src='$dataShopLogo' title='shop logo'></a>";
          $expiryDate = new Zend_Date($offer['endDate']);
          $expireDate[$offerIndex]['name'] = 'expDate_'.($offerIndex+1);
          $expireDate[$offerIndex]['content'] = 
              FrontEnd_Helper_viewHelper::__email('email_expires on:') ." " . $expiryDate->get(Zend_Date::DATE_LONG);
          $shopPermalink[$offerIndex]['name'] = 'shopPermalink_'.($offerIndex+1);
          $shopPermalink[$offerIndex]['content'] = $permalinkEmail;
       }
       return array(
             'dataShopName' => $dataShopName,
             'dataShopImage' => $dataShopImage,
             'shopPermalink' => $shopPermalink,
             'expDate' => $expireDate,
             'dataOfferName' =>  $dataOfferName
       );
    }
}
