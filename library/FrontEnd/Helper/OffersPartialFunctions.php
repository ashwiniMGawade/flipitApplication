<?php
class FrontEnd_Helper_OffersPartialFunctions extends FrontEnd_Helper_viewHelper
{
    public function getUrlToShow($currentOffer)
    {
        if ($currentOffer->refURL != "" || $currentOffer->shop['refUrl']!= "" || $currentOffer->shop['actualUrl'] != "") {
            $urlToShow = self::getOfferBounceUrl($currentOffer->id);
        } else {
            $urlToShow = HTTP_PATH_LOCALE.$currentOffer->shop['permalink'];
        }
        if ($currentOffer->discountType=='PA' || $currentOffer->discountType=='PR') {
            if ($currentOffer->refOfferUrl != null) {
                $urlToShow = self::getOfferBounceUrl($currentOffer->id);
            } elseif (count($currentOffer->logo)>0) {
                $urlToShow = PUBLIC_PATH_CDN.ltrim($currentOffer->logo['path'], "/").$currentOffer->logo['name'];
            }
        }
        return $urlToShow;
    }

    public function getOfferBounceUrl($offerId)
    {
        return HTTP_PATH_LOCALE."out/offer/".$offerId;
    }

    public function getDiscountImage($currentOffer)
    {
        $offerDiscountImage ='';
        if (!empty ( $currentOffer->tiles)) {
            $offerDiscountImage = PUBLIC_PATH_CDN . ltrim($currentOffer->tiles['path'], "/").$currentOffer->tiles['name'];
        }
        return $offerDiscountImage;
    }

    public function getOfferTermsAndCondition($currentOffer)
    {
        $termsAndConditions = '';
        if(isset($currentOffer->termandcondition)) {
            if (count($currentOffer->termandcondition) > 0) {
                $termsAndConditions = $currentOffer->termandcondition[0]['content'];
            }
        }    
        return $termsAndConditions;
    }

    public function getDaysTillOfferExpires($endDate)
    {
        $currentDate = date('Y-m-d');
        $offerEndDate = date('Y-m-d', strtotime($endDate));
        $timeStampStart = strtotime($offerEndDate);
        $timeStampEnd = strtotime($currentDate);
        $dateDifference = abs($timeStampEnd - $timeStampStart);
        $daysTillOfferExpires = floor($dateDifference/(60*60*24));
        return $daysTillOfferExpires;
    }

    public function getUserIsLoggedInOrNot()
    {
        $userLoginStatus = false;
        if (Auth_VisitorAdapter::hasIdentity()) :
            $userLoginStatus = true;
        endif;
        return $userLoginStatus;
    }

    public function getOfferOption($offerOption)
    {
        $offerOptionHtml = '<strong class="exclusive">
        <span class="glyphicon glyphicon-star"></span>'.$offerOption.'</strong>';
        return $offerOptionHtml;
    }

    public function getOfferExclusiveOrEditor($currentOffer)
    {
        $offerOption = '';
        if ($currentOffer->exclusiveCode == '1'):
            $offerOption = self::getOfferOption($this->zendTranslate->translate('Exclusive'));
        elseif ($currentOffer->editorPicks =='1'):
        $offerOption = self::getOfferOption($this->zendTranslate->translate('Editor'));
        endif;
        return $offerOption;
    }

    public function getOfferDates($currentOffer, $daysTillOfferExpires)
    {
        $stringAdded = $this->zendTranslate->translate('Added');
        $stringOnly = $this->zendTranslate->translate('Only');
        $startDate = new Zend_Date(strtotime($currentOffer->startDate));
        $offerDates = '';
        if($currentOffer->discountType == "CD"):
            $offerDates .= $stringAdded;
            $offerDates .= ': ';
            $offerDates .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
            $offerDates .= ', ';

        if ($daysTillOfferExpires ==5 || $daysTillOfferExpires ==4 || $daysTillOfferExpires ==3 || $daysTillOfferExpires ==2) {
            $offerDates .= $stringOnly;
            $offerDates .= '&nbsp;';
            $offerDates .= $daysTillOfferExpires;
            $offerDates .= '&nbsp;';
            $offerDates .= $this->zendTranslate->translate('days valid!');

        } elseif ($daysTillOfferExpires == 1) {
            $offerDates .= $stringOnly;
            $offerDates .= '&nbsp;';
            $offerDates .= $daysTillOfferExpires;
            $offerDates .= '&nbsp;';
            $offerDates .= $this->zendTranslate->translate('day only!');

        } elseif ($daysTillOfferExpires == 0) {
                $offerDates .= $this->zendTranslate->translate('Expires today');
        } else {
                $endDate = new Zend_Date(strtotime($currentOffer->endDate));
                $offerDates .= $this->zendTranslate->translate('Expires on').': ';
                $offerDates .= ucwords($endDate->get(Zend_Date::DATE_MEDIUM));
        } elseif ($currentOffer->discountType == "PR" || $currentOffer->discountType == "SL" || $currentOffer->discountType == "PA"):
            $offerDates .= $stringAdded;
            $offerDates .= ': ';
            $offerDates .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
        endif;
        return $offerDates;
    }
    public function getOfferOptionAndOfferDates($currentOffer, $daysTillOfferExpires)
    {
        $offerOption = self::getOfferExclusiveOrEditor($currentOffer);
        $offerDates = self::getOfferDates($currentOffer, $daysTillOfferExpires);
        return $offerOption . $offerDates;
    }
    public function getCssClassNameForOffer($currentOffer, $offerType)
    {
        $className = 'code';
        $className .= $offerType=='simple' || $offerType=='extendedOffer' ? '' : ' code-2';
        return $className;
    }

    public function getOfferImage($currentOffer, $offersType)
    {
         $offerImageDiv = '';
        if ($offersType == 'simple' || $offersType == 'extendedOffer') {
            $offerDiscountImage = self::getDiscountImage($currentOffer);
            $altAttributeText = $currentOffer->tiles['label'];
            $offerImageDiv = self::getImageTag($offerDiscountImage, $altAttributeText, false);
        } else {
            $offerDiscountImage = self::getShopLogoForOffer($currentOffer);
            $altAttributeText = $currentOffer->shop['name'];
            $imageTag = self::getImageTag($offerDiscountImage, $altAttributeText, true);
            $offerImageDiv = $imageTag . '<footer class="bottom">' . self::getOfferTypeText($currentOffer) . '</footer>';
        }
      
        return $offerImageDiv;
    }

    public function getShopLogoForOffer($currentOffer)
    {
        return PUBLIC_PATH_CDN.ltrim($currentOffer->shop['logo']['path'], "/").'thum_medium_store_'. $currentOffer->shop['logo']['name'];
    }
    
    public function getImageTag($offerDiscountImage, $altAttributeText, $shopCodeHolder)
    {
        $imageTagForOffer = '';
        if ($shopCodeHolder) {
            $imageTag ='<img width="130" height="68" src="'.$offerDiscountImage.'" alt="'.$altAttributeText.'"/>';
            $imageTagForOffer = '<div class="center"><div class="code-holder">' . $imageTag . '</div></div>';
        } else {
            $imageTagForOffer ='<img src="'.$offerDiscountImage.'" alt="'.$altAttributeText.'"/>';
        }
        return $imageTagForOffer;
    }

    public function getOfferTypeText($currentOffer)
    {
        
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $offerTypeText = $this->zendTranslate->translate('printable');
        } elseif ($currentOffer->discountType=='SL') {
            $offerTypeText = $this->zendTranslate->translate('sale');
        } elseif (isset($currentOffer->extendedOffer) ? $currentOffer->extendedOffer =='1' : '') {
            $offerTypeText = $this->zendTranslate->translate('deal');
        } else {
            $offerTypeText = 'code';
        }
        return $offerTypeText;
    }

    public function getSimilarShopHeader($shopName, $offersType)
    {
        $similarShopHeader = '';
        if ($offersType == 'similar') {
            $similarShopHeader = '<header class="heading-box text-coupon">
            <h2>'.$this->zendTranslate->translate('Coupon codes for similar stores').'</h2>
            <strong>'.$this->zendTranslate->translate('Similar vouchers and discounts for'). ' ' . $shopName .'</strong>
            </header>';
        }
        return $similarShopHeader;
    }

    public function getShopLogoForSignUp($shop)
    {
        $imgTagWithImage = '';
        if ($shop!=null) {
            $shopLogoImage = PUBLIC_PATH_CDN.ltrim($shop['logo']['path'], "/").'thum_medium_'. $shop['logo']['name'];
            $imgTagWithImage = '<img alt="' . $shop['logo']['name']. '" src="'. $shopLogoImage .'">';
        } else {
            $imgTagWithImage = '<div class="ico-mail"></div>';
        }
        return $imgTagWithImage;
    }

    public function getPopupLink($currentOffer, $urlToShow)
    {
        if ($currentOffer->discountType == "CD") {
            $popupLink = "?popup=$currentOffer->id&type=code";
        } elseif ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $popupLink = "?popup=$currentOffer->id&printable=$urlToShow";
        } else {
            $popupLink = '';
        }
        return $popupLink;
    }

    public function getmainOfferLink($currentOffer, $urlToShow, $offerBounceRate, $popupLink, $offerAnchorTagContent, $class)
    {
        
        if ($currentOffer->discountType == "CD" ) {
            $onClick =  $currentOffer->discountType == "SL" ? "showCodeInformation($currentOffer->id)," : " ";
            $onClick .= "viewCounter('onclick', 'offer', $currentOffer->id), ga('send', 'event', 'aff', '$offerBounceRate'),OpenInNewTab('".HTTP_PATH_LOCALE.$currentOffer->shop['permalink'].$popupLink."')";
            $offerLink = '<a id="'.$currentOffer->id.'" class="'.$class.'" href="'.$urlToShow.'" vote="0" rel="nofollow" target="_self" onClick="'.$onClick.'">
            '.$offerAnchorTagContent.' </a>';
        } elseif ($currentOffer->discountType == "SL") {
            $onClick = "viewCounter('onclick', 'offer', $currentOffer->id), ga('send', 'event', 'aff', '$offerBounceRate')";
            $offerLink = '<a id="'.$currentOffer->id.'" class="'.$class.'" href="'.$urlToShow.'" vote="0" rel="nofollow" target="_blank" onClick="'.$onClick.'">
            '.$offerAnchorTagContent.' </a>';
        } else {
            $onClick =  self::getUserIsLoggedInOrNot() == "true" ? "OpenInNewTab('".HTTP_PATH_LOCALE.$currentOffer->shop['permalink'].$popupLink."')" : HTTP_PATH_LOCALE."accountlogin" ;
            $offerLink = '<a id="'.$currentOffer->id.'" class="'.$class.'" vote = "0" href= "'.$urlToShow.'" alt = "'.$urlToShow.'" target="_self" onclick = "'.$onClick.'" rel="nofollow">'.$offerAnchorTagContent.'</a>';
        }
        return $offerLink;
    }

    public function getRedirectUrlforOffer($currentOffer, $urlToShow, $offerBounceRate, $offerAnchorTagContent, $class)
    {
        
        $popupLink = self::getPopupLink($currentOffer, $urlToShow);
        echo $mainOfferLink = self::getmainOfferLink($currentOffer, $urlToShow, $offerBounceRate, $popupLink, $offerAnchorTagContent, $class);
        return $mainOfferLink;
    }

    public function getCommonRedirectUrlForOffer($currentOffer, $urlToShow, $offerBounceRate, $offerAnchorTagContent, $type)
    {
        $redirectUrl = '';
        switch ($type){
            case 'mainOfferClickoutButton':
                $redirectUrl = self::getRedirectUrlforOffer($currentOffer, $urlToShow, $offerBounceRate, $this->zendTranslate->translate('>Get code &amp; Open site'), "btn blue btn-primary");
            break;
            case 'offerTitle':
                $redirectUrl = self::getRedirectUrlforOffer($currentOffer, $urlToShow, $offerBounceRate, $offerAnchorTagContent, "link");
            break;
            case 'offerImage':
                $redirectUrl = self::getRedirectUrlforOffer($currentOffer, $urlToShow, $offerBounceRate, self::getOfferImage($currentOffer, $offerAnchorTagContent), self::getCssClassNameForOffer($currentOffer, $offerAnchorTagContent));
            break;
            default: break;
            return $redirectUrl;
        }
       
    }


    public function getSecondButtonforOffer($currentOffer, $urlToShow, $offerBounceRate, $permalink)
    {
        $buttonWithCodeforOffer = '';
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $onClick =  self::getUserIsLoggedInOrNot() == "true" ? "printIt('$urlToShow');" : "printIt('$urlToShow');";
            $buttonWithCodeforOffer = '<a class="btn btn-default btn-print" onclick ="'.$onClick.'"  >'.$this->zendTranslate->translate('print now').'<span class="ico-print"></span></a>';
        } else if ($currentOffer->discountType=='CD') {
            $onClick = "showCodeInformation($currentOffer->id), showCodePopUp(this), ga('send','event', 'aff','$offerBounceRate'),OpenInNewTab('". $permalink. "?popup=$currentOffer->id&type=code')";
            $buttonWithCodeforOffer = '<a id="'.$currentOffer->id.'" class = "btn orange btn-warning btn-code" vote="0" href="'.$urlToShow.'" rel="nofollow" target="_self" onClick="'.$onClick.'">'.$this->zendTranslate->translate('Pack this offer').'</a>';
        } else if ($currentOffer->discountType == "SL") {
            $buttonWithCodeforOffer = '';
        }
        return $buttonWithCodeforOffer;
    }

    public function getTermAndConditionsLink($currentOffer, $termsAndConditions)
    {
        $termAndConditionLink ='';
        if (isset($currentOffer->userGenerated) ? $currentOffer->userGenerated == 0 : '' &&  $termsAndConditions!='' && $termsAndConditions!=null) {
            $termAndConditionLink = '
            <a id="termAndConditionLink'.$currentOffer->id .'" onclick="showTermAndConditions('.$currentOffer->id.')" class="terms"
            href="javascript:void(0);">'. $this->zendTranslate->translate('Term') . '&amp;' .$this->zendTranslate->translate('Conditions').'</a>';
            if ($termsAndConditions!='' && $termsAndConditions!=null && $currentOffer->extendedOffer =='1') {
                $termAndConditionLink.='&nbsp; | &nbsp;';
            }
        }
        return $termAndConditionLink;
    }

    public function getExtendedOfferLink($currentOffer)
    {
        $extendedOfferLink = '';
        if (isset($currentOffer->extendedOffer) ? $currentOffer->extendedOffer =='1' : ''):
            $extendedOfferLink ='<a class="text-blue-link"
            href="'.HTTP_PATH_LOCALE .FrontEnd_Helper_viewHelper::__link('deals').'/'. $currentOffer->extendedUrl.'">'
            .$this->zendTranslate->translate('More about this code').'</a>';
        endif;
        return $extendedOfferLink;
    }
    
    public function getViewAllCodeLink($shopName)
    {
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        return $viewAllLink ='<a href="'.$domainName.$shopName.'">'. $this->zendTranslate->translate("View all ") . $shopName . $this->zendTranslate->translate(" Voucher Codes").'</a>';
    }
    
    public function getExpiredOfferMessage($endDate, $currentDate)
    {
        $expiredOfferMessage= '';
        if ($endDate < $currentDate) {
            $expiredOfferMessage = '<div class="message">
            <div class="holder">
            <span class="ico-warning"></span>
            <span class="text">'.$this->zendTranslate->translate("Sorry, this coupon is already expired. Maybe the following coupon can help you"). '</span>
            </div>
            </div>';
        }
        return $expiredOfferMessage;
    }
    public function getSiteLogoforPopup()
    {
        if (LOCALE == '') {
            $siteImage = "";
        } else {
            $siteImage = '<img class="logo-img" src="'.HTTP_PATH.'public/images/logo-3.png" width="109" height="49" alt="Flipit">';
        }
        return $siteImage;
    }
    
}
