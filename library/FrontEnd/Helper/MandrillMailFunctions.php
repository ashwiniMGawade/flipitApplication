<?php
class FrontEnd_Helper_MandrillMailFunctions extends FrontEnd_Helper_viewHelper {
    public $imageLogoForMail = '';
    public $siteName = '';
    public function __construct() {
        $this->imageLogoForMail =
            "<a href=".HTTP_PATH_LOCALE.">
                <img alt='flipit-welcome' src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/>
            </a>";
        $this->siteName = "Flipit.com";
        if (HTTP_HOST == "www.kortingscode.nl") {
            $this->imageLogoForMail = 
                "<a href=".HTTP_PATH_LOCALE.">
                    <img alt='HeaderMail' src='".HTTP_PATH."public/images/HeaderMail.gif'/>
                </a>";
            $this->siteName = "Kortingscode.nl";
        }
        parent::__construct();
    }
    public function sendForgotPasswordMail($visitorId, $emailAddress, $currentController) {
        $mailData = array(
                array(
                    'name'=> 'headerWelcome',
                    'content'=>$this->imageLogoForMail),
                array('name'=>'bestRegards',
                    'content'=>
                        $this->zendTranslate->translate('Best').' '.$this->siteName.' '
                        .$this->zendTranslate->translate('visitor,')
                ),
                array(
                    'name'=> 'centerContent',
                    'content'=>
                        $this->zendTranslate->translate('No problem you have forgotten your password, use the following link you can set it up again:').
                        '<a href="'
                        . HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'
                        .FrontEnd_Helper_viewHelper::__link('resetpassword').'/'
                        .base64_encode($visitorId)
                        . '">'.$this->zendTranslate->translate('Click Here').'</a>'
                ),
                array(
                    'name'=> 'bottomContent',
                    'content'=> $this->zendTranslate->translate('Greetings').',<br><br>'
                        . $this->zendTranslate->translate('The editors of Kortingscode.nl')
                ),
                array(
                    'name'=> 'copyright',
                    'content'=>$this->zendTranslate->translate('Copyright &copy; 2013').' '.$this->siteName.'. '
                        .$this->zendTranslate->translate('All Rights Reserved.')
                ),
                array(
                    'name'=> 'address',
                    'content'=>$this->zendTranslate->translate("You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                        '<br/>' . $this->zendTranslate->translate("Offers.")
                        . '<a href='.HTTP_PATH_LOCALE.' style="color:#ffffff; padding:0 2px;">'.$this->siteName.'</a>'
                        . $this->zendTranslate->translate('is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
                ),
                array(
                    'name'=> 'logincontact',
                    'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;"
                        href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'">'
                        .$this->zendTranslate->translate('login').'</a>'
                ));
        $emailData = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $emailData[0]['emailperlocale'];
        self::sendTemplate(
            $currentController,
            'welcomeTemplate',
            $mailData,
            $emailFrom,
            $this->zendTranslate->translate('Forgot-Password'),
            $emailAddress,
            $this->zendTranslate->translate('Member'),
            $this->zendTranslate->translate('Password Change')
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
                'content'=>$this->zendTranslate->translate('Best news reader, ')
            ),
            array(
                'name'=>'centerContent',
                'content'=>$this->zendTranslate->translate('From now on you will receive our weekly newsletter with the best discount codes.')
            ),
            array(
                'name'=>'bottomContent',
                'content'=>$this->zendTranslate->translate('Thanks').', <br/>'.$this->siteName
            ),
            array(
                'name'=>'copyright',
                'content'=>$this->zendTranslate->translate('Copyright &copy; 2013').' '.$this->siteName.'. '
                    .$this->zendTranslate->translate('All Rights Reserved.')
            ),
            array(
                'name'=>'address',
                'content'=>
                    $this->zendTranslate->translate("You receive this newsletter because you have given to you to keep abreast of our latest us your consent").
                    '<br/>' . $this->zendTranslate->translate("Offers.")
                    . '<a href='.HTTP_PATH_LOCALE.' style="color:#ffffff; padding:0 2px;">'.$this->siteName.'</a>' 
                    . $this->zendTranslate->translate('is part of Imbull, Weteringschans 120, 1017 XT Amsterdam - Chamber of Commerce 34,339,618')
            ),
            array(
                'name'=>'logincontact',
                'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;" 
                    href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'
                    .FrontEnd_Helper_viewHelper::__link('directlogin'). "/" . base64_encode($visitorDetails[0]['email']) 
                    ."/". $visitorDetails[0]['password'].'">'.$this->zendTranslate->translate('My Profile').'</a>'
            )
        );
        $staticContent = array(
            array(
                'name' => 'moreOffersLink',
                'content' => HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('populair')
            ),
           array(
               'name' => 'moreOffers',
               'content' => $this->zendTranslate->translate('See more of our top offers') . ' >'
           )
        );
        $poupularTitle = array(
            array(
                'name' => 'poupularTitle',
                'content' => $this->zendTranslate->translate('Top 5 Discount Codes:')
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
            $this->zendTranslate->translate('welcome'),
            $visitorDetails[0]['email'],
            !empty($visitorDetails[0]['firstName']) ? $visitorDetails[0]['firstName'] :$this->zendTranslate->translate('Member'),
            $this->zendTranslate->translate('Welcome e-mail subject')
        );
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
          $dataShopImage[$offerIndex]['content'] = "<a href='$permalinkEmail'><img src='$dataShopLogo'></a>";
          $expiryDate = new Zend_Date($offer['endDate']);
          $expireDate[$offerIndex]['name'] = 'expDate_'.($offerIndex+1);
          $expireDate[$offerIndex]['content'] = 
              FrontEnd_Helper_viewHelper::__link('Verloopt op:') ." " . $expiryDate->get(Zend_Date::DATE_MEDIUM);
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
