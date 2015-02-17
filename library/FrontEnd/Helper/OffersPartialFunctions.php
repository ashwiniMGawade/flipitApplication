<?php
class FrontEnd_Helper_OffersPartialFunctions
{
    public function getUrlToShow($currentOffer, $constants = HTTP_PATH_LOCALE)
    {
        if (
            $currentOffer->refURL != ""
            || $currentOffer->shop['refUrl']!= ""
            || $currentOffer->shop['actualUrl'] != ""
        ) {
            $urlToShow = self::getOfferBounceUrl($currentOffer->id, $constants);
        } else {
            $urlToShow = $constants.$currentOffer->shop['permalink'];
        }
        if ($currentOffer->discountType=='PA' || $currentOffer->discountType=='PR') {
            if ($currentOffer->refOfferUrl != null) {
                $urlToShow = self::getOfferBounceUrl($currentOffer->id);
            } else if (count($currentOffer->logo)>0) {
                $urlToShow = PUBLIC_PATH_CDN.ltrim($currentOffer->logo['path'], "/").$currentOffer->logo['name'];
            }
        }
        return $urlToShow;
    }

    public function getOfferBounceUrl($offerId, $constants = HTTP_PATH_LOCALE)
    {
        return $constants."out/offer/".$offerId;
    }

    public function getDiscountImage($currentOffer)
    {
        $offerDiscountImage ='';
        if (!empty ( $currentOffer->tiles)) {
            $offerDiscountImage =
            PUBLIC_PATH_CDN . ltrim($currentOffer->tiles['path'], "/").$currentOffer->tiles['name'];
        }
        return $offerDiscountImage;
    }

    public function getOfferTermsAndCondition($currentOffer)
    {
        $termsAndConditions = '';
        if (isset($currentOffer->termandcondition)) {
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

    public function getOfferOption($offerOption, $type)
    {
        if ($type == 'ex' || $type == 'ed') {
            $offerOptionHtml = '<strong class="exclusive">
            <span class="glyphicon glyphicon-star"></span>'.$offerOption.'</strong>';
        } else if ($type == 'sc') {
            $offerOptionHtml = '<strong class="social-color">
            <span class="social-icon"></span>'.$offerOption.'</strong>';
        }
        return $offerOptionHtml;
    }

    public function getOfferExclusiveOrEditor($currentOffer)
    {
        $offerOption = '';
        if ($currentOffer->exclusiveCode == '1'):
            $offerOption = self::getOfferOption(FrontEnd_Helper_viewHelper::__translate('Exclusive'), 'ex');
        elseif ($currentOffer->editorPicks =='1'):
            $offerOption = self::getOfferOption(FrontEnd_Helper_viewHelper::__translate('Editor'), 'ed');
        elseif ($currentOffer->userGenerated =='1'):
            $offerOption = self::getOfferOption(FrontEnd_Helper_viewHelper::__translate('Social Code'), 'sc');
        endif;
        return $offerOption;
    }

    public function getOfferDates($currentOffer, $daysTillOfferExpires)
    {
        $stringAdded = FrontEnd_Helper_viewHelper::__translate('Added');
        $stringOnly = FrontEnd_Helper_viewHelper::__translate('Only');
        $startDate = new Zend_Date(strtotime($currentOffer->startDate));
        $offerDates = '';
        if($currentOffer->discountType == "CD"):
            $offerDates .= $stringAdded;
            $offerDates .= ': ';
            $offerDates .= ucwords($startDate->get(Zend_Date::DATE_LONG));
            $offerDates .= ', ';

            if (
                $daysTillOfferExpires ==5
                || $daysTillOfferExpires ==4
                || $daysTillOfferExpires ==3
                || $daysTillOfferExpires ==2
            ) {
                $offerDates .= $stringOnly;
                $offerDates .= '&nbsp;';
                $offerDates .= $daysTillOfferExpires;
                $offerDates .= '&nbsp;';
                $offerDates .= FrontEnd_Helper_viewHelper::__translate('days left!');
            } elseif ($daysTillOfferExpires == 1) {
                $offerDates .= $stringOnly;
                $offerDates .= '&nbsp;';
                $offerDates .= $daysTillOfferExpires;
                $offerDates .= '&nbsp;';
                $offerDates .= FrontEnd_Helper_viewHelper::__translate('day left!');

        } elseif ($daysTillOfferExpires == 0) {
                $offerDates .= FrontEnd_Helper_viewHelper::__translate('Expires today');
            } else {
                $endDate = new Zend_Date(strtotime($currentOffer->endDate));
                $offerDates .= FrontEnd_Helper_viewHelper::__translate('Expires on').': ';
                $offerDates .= ucwords($endDate->get(Zend_Date::DATE_LONG));
            } elseif (
                $currentOffer->discountType == "PR"
                || $currentOffer->discountType == "SL"
                || $currentOffer->discountType == "PA"
            ):
            $offerDates .= $stringAdded;
            $offerDates .= ': ';
            $offerDates .= ucwords($startDate->get(Zend_Date::DATE_LONG));
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
            $altAttributeText = isset($currentOffer->tiles['label']) ? $currentOffer->tiles['label'] : '';
            if ($currentOffer->userGenerated == 1 and $currentOffer->approved == '0') {
                $offerDiscountImage = HTTP_PATH ."public/images/front_end/box_bg_orange_16.png";
                $altAttributeText = 'Social code';
                $offerImageDiv = self::getImageTag($offerDiscountImage, $altAttributeText, false);
            } else {
                $offerImageDiv = self::getImageTag($offerDiscountImage, $altAttributeText, false);
            }
        } else {
            $offerDiscountImage = self::getShopLogoForOffer($currentOffer);
            $altAttributeText = $currentOffer->shop['name'];
            $imageTag = self::getImageTag($offerDiscountImage, $altAttributeText, true);
            $offerImageDiv =
                $imageTag . '<footer class="bottom">'
                . FrontEnd_Helper_viewHelper::__translate(self::getOfferTypeText($currentOffer)) . '</footer>';
        }
      
        return $offerImageDiv;
    }

    public function getShopLogoForOffer($currentOffer)
    {
        return
            PUBLIC_PATH_CDN.ltrim($currentOffer->shop['logo']['path'], "/").'thum_medium_store_'
            . $currentOffer->shop['logo']['name'];
    }
    
    public function getImageTag($offerDiscountImage, $altAttributeText, $shopCodeHolder)
    {
        $imageTagForOffer = '';
        if ($shopCodeHolder) {
            $imageTag ='<img width="130" height="68" src="'.$offerDiscountImage.'" alt="'.$altAttributeText.'" 
            title="'.$altAttributeText.'"/>';
            $imageTagForOffer = '<div class="center"><div class="code-holder">' . $imageTag . '</div></div>';
        } else {
            $imageTagForOffer ='<img class="small-code" src="'.$offerDiscountImage.'" alt="'.$altAttributeText.'"
            title="'.$altAttributeText.'"/>';
        }
        return $imageTagForOffer;
    }

    public function getOfferTypeText($currentOffer)
    {
        
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $offerTypeText = FrontEnd_Helper_viewHelper::__translate('printable');
        } else if ($currentOffer->discountType=='SL') {
            $offerTypeText = FrontEnd_Helper_viewHelper::__translate('sale');
        } else if (isset($currentOffer->extendedOffer) ? $currentOffer->extendedOffer =='1' : '') {
            $offerTypeText = FrontEnd_Helper_viewHelper::__translate('deal');
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
            <h2>'.FrontEnd_Helper_viewHelper::__translate('Coupon codes for similar stores').'</h2>
            <strong>'.FrontEnd_Helper_viewHelper::__translate('Similar vouchers and discounts for'). ' '
            . $shopName .'</strong>
            </header>';
        }
        return $similarShopHeader;
    }

    public function getShopLogoForSignUp($shop)
    {
        $imgTagWithImage = '';
        if ($shop!=null) {
            $shopLogoImage = PUBLIC_PATH_CDN.ltrim($shop['logo']['path'], "/").'thum_medium_'. $shop['logo']['name'];
            $imgTagWithImage = '<img alt="' . $shop['permaLink']. '" src="'. $shopLogoImage .'" 
            title="' . $shop['permaLink']. '">';
        } else {
            $imgTagWithImage = '<div class="ico-mail"></div>';
        }
        return $imgTagWithImage;
    }

    public function getPopupLink($currentOffer, $urlToShow)
    {
        if ($currentOffer->discountType == "CD") {
            $popupLink = $currentOffer->couponCodeType == 'UN'
            ? "?popup=$currentOffer->id&codetype=$currentOffer->couponCodeType&type=code#$currentOffer->id"
            : "?popup=$currentOffer->id&type=code#$currentOffer->id";
        } else if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $popupLink = "?popup=$currentOffer->id&printable=$urlToShow#$currentOffer->id";
        } else {
            $popupLink = '';
        }
        return $popupLink;
    }

    public function getmainOfferLink(
        $currentOffer,
        $urlToShow,
        $offerBounceRate,
        $popupLink,
        $offerAnchorTagContent,
        $class,
        $offerImage
    ) {
        $headOpen = '';
        $headClose = '';
        $offerAnchorText = $offerAnchorTagContent;
        if ($class=='link clickout-title') {
            $headOpen = '<h3 class="'. $class .'">';
            $headClose = '</h3>';
        }
        if ($offerImage == 'offerImage') {
            $offerLink =
                '<div class="'.$class.'">
                '.$offerAnchorTagContent.' </div>';
        } else {
            $visitorInformation = '';
            if (!empty(Auth_VisitorAdapter::getIdentity()->id)) {
                $visitorInformation = Visitor::getUserDetails(Auth_VisitorAdapter::getIdentity()->id);
            }
            if (empty($visitorInformation) && isset($currentOffer->Visability) && $currentOffer->Visability == 'MEM') {
                $offerLink =
                    '<span class="'.$class.'">
                '.$offerAnchorText.' </span>';
            } else {
                if ($currentOffer->discountType == "CD") {
                    $onClick = $currentOffer->discountType == "SL" ? "showCodeInformation($currentOffer->id)," : " ";
                    $onClick .= "viewCounter('onclick', 'offer', $currentOffer->id),
                    ga('send', 'event', 'aff', '$offerBounceRate'),
                    OpenInNewTab('".HTTP_PATH_LOCALE.$currentOffer->shop['permalink'].$popupLink."')";
                    if ($currentOffer->userGenerated == 1 && $currentOffer->approved == '0') {
                        $offerLink ='<span class="'.$class.'">'.$offerAnchorText.' </span>';
                    } else {
                        $offerLink =
                            '<a  id="'.$currentOffer->id.'" class="'.$class.'" 
                            href="'.$urlToShow.'" vote="0" rel="nofollow" 
                            target="_self" onClick="'.$onClick.'">
                        '.$offerAnchorText.' </a>';
                    }
                } else if ($currentOffer->discountType == "SL") {
                    if ($class == "btn blue btn-primary") {
                        $offerAnchorTagContent = FrontEnd_Helper_viewHelper::__translate('Click to Visit Sale');
                        $offerAnchorText = FrontEnd_Helper_viewHelper::__translate('Click to Visit Sale');
                    }
                    $onClick = "viewCounter('onclick', 'offer', $currentOffer->id),
                    ga('send', 'event', 'aff', '$offerBounceRate')";
                    $offerLink =
                        '<a id="'.$currentOffer->id.'" class="'.$class.'" 
                        href="'.$urlToShow.'" vote="0" rel="nofollow" target="_blank" onClick="'.$onClick.'">
                     '.$offerAnchorText.'</a>';
                } else {
                    if ($class == "btn blue btn-primary") {
                        $offerAnchorTagContent = FrontEnd_Helper_viewHelper::__translate('Click to View Information');
                        $offerAnchorText = FrontEnd_Helper_viewHelper::__translate('Click to View Information');
                    }
                    $onClick =
                        self::getUserIsLoggedInOrNot() == "true"
                        ? "OpenInNewTab('".HTTP_PATH_LOCALE.$currentOffer->shop['permalink'].$popupLink."')"
                        : HTTP_PATH_LOCALE."accountlogin";
                    $offerLink =
                        '<a id="'.$currentOffer->id.'" class="'.$class.'" vote = "0" href= "'.$urlToShow.'" 
                        alt = "'.$urlToShow.'" target="_blank" onclick = "'.$onClick.'" rel="nofollow">
                        '.$offerAnchorText .'</a>';
                }
            }
        }
        return $headOpen. $offerLink . $headClose;
    }

    public function getRedirectUrlforOffer(
        $currentOffer,
        $urlToShow,
        $offerBounceRate,
        $offerAnchorTagContent,
        $class,
        $offerImage = ''
    ) {
        
        $popupLink = self::getPopupLink($currentOffer, $urlToShow);
        echo $mainOfferLink = self::getmainOfferLink(
            $currentOffer,
            $urlToShow,
            $offerBounceRate,
            $popupLink,
            $offerAnchorTagContent,
            $class,
            $offerImage
        );
        return $mainOfferLink;
    }

    public function getCommonRedirectUrlForOffer(
        $currentOffer,
        $urlToShow,
        $offerBounceRate,
        $offerAnchorTagContent,
        $type
    )
    {
        $redirectUrl = '';
        switch ($type){
            case 'mainOfferClickoutButton':
                $redirectUrl = self::getRedirectUrlforOffer(
                    $currentOffer,
                    $urlToShow,
                    $offerBounceRate,
                    FrontEnd_Helper_viewHelper::__translate('Get code &amp; Open site'),
                    "btn blue btn-primary"
                );
                break;
            case 'offerTitle':
                $redirectUrl = self::getRedirectUrlforOffer(
                    $currentOffer,
                    $urlToShow,
                    $offerBounceRate,
                    $offerAnchorTagContent,
                    "link clickout-title"
                );
                break;
            case 'offerImage':
                $redirectUrl = self::getRedirectUrlforOffer(
                    $currentOffer,
                    $urlToShow,
                    $offerBounceRate,
                    self::getOfferImage($currentOffer, $offerAnchorTagContent),
                    self::getCssClassNameForOffer($currentOffer, $offerAnchorTagContent),
                    'offerImage'
                );
                break;
            default:
                break;
            return $redirectUrl;
        }
    }


    public function getSecondButtonforOffer($currentOffer, $urlToShow, $offerBounceRate, $permalink)
    {
        $buttonWithCodeforOffer = '';
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $onClick =
                self::getUserIsLoggedInOrNot() == "true" ? "printIt('$urlToShow');" : "printIt('$urlToShow');";
            $buttonWithCodeforOffer = '<a class="btn btn-default btn-print" onclick ="'.$onClick.'"  >'
                .FrontEnd_Helper_viewHelper::__translate('print now').'<span class="ico-print"></span>
            </a>';
        } else if ($currentOffer->discountType=='CD') {
            $popupLink = self::getPopupLink($currentOffer, $urlToShow);
            $onClick =
                "showCodeInformation($currentOffer->id), showCodePopUp(this),
                ga('send','event', 'aff','$offerBounceRate'),
                OpenInNewTab('".HTTP_PATH_LOCALE. $permalink.$popupLink."')";

            if ($currentOffer->userGenerated == 1 && $currentOffer->approved == '0') {
                 $buttonWithCodeforOffer ='<span class="btn orange btn-warning btn-code"></span>';
            } else {
                $buttonWithCodeforOffer =
                '<a id="'.$currentOffer->id.'" 
                class = "btn orange btn-warning btn-code" vote="0" href="'.$urlToShow.'" 
                rel="nofollow" target="_self" onClick="'.$onClick.'">'
                .FrontEnd_Helper_viewHelper::__translate('Get this offer').'</a>';
            }
        } else if ($currentOffer->discountType == "SL") {
            $buttonWithCodeforOffer = '';
        }
        return $buttonWithCodeforOffer;
    }

    public function getTermAndConditionsLink($currentOffer, $termsAndConditions)
    {
        $termAndConditionLink ='';
        if ($termsAndConditions!=''
            && $termsAndConditions!=null
        ) {
            $termAndConditionLink = '<li>
            <a id="termAndConditionLink'.$currentOffer->id
            .'" onclick="showTermAndConditions('.$currentOffer->id.')" class="terms"
            href="javascript:void(0);">'
            . FrontEnd_Helper_viewHelper::__translate('Terms &amp; Conditions') . '</a>';
            if ($termsAndConditions!='' && $termsAndConditions!=null && $currentOffer->extendedOffer =='1') {
                $termAndConditionLink.='&nbsp; - &nbsp;';
            }
        }
        return $termAndConditionLink ."</li>";
    }

    public function getExtendedOfferLink($currentOffer)
    {
        $extendedOfferLink = '';
        if (isset($currentOffer->extendedOffer) ? $currentOffer->extendedOffer =='1' : ''):
            $extendedOfferLink ='<li><a class="text-blue-link"
            href="'.HTTP_PATH_LOCALE .FrontEnd_Helper_viewHelper::__link('link_deals').'/'
            . $currentOffer->extendedUrl.'">'
            .FrontEnd_Helper_viewHelper::__translate('More about this code').'</a></li>';
        endif;
        return $extendedOfferLink;
    }
    
    public function getViewAllCodeLink($shopName, $shopPermalink, $showHyphen)
    {
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        return $viewAllLink = $showHyphen.'<li>'
            . FrontEnd_Helper_viewHelper::__translate("View all")
            .' <a href="'.$domainName.$shopPermalink.'">'. $shopName . ' '
            . FrontEnd_Helper_viewHelper::__translate("Voucher Codes").'</a></li>';
    }
    
    public function getExpiredOfferMessage($endDate, $currentDate)
    {
        $expiredOfferMessage= '';
        if ($endDate < $currentDate) {
            $expiredOfferMessage = '<div class="message">
            <div class="holder">
            <span class="ico-warning"></span>
            <span class="text">'
            .FrontEnd_Helper_viewHelper::__translate("Sorry, this coupon is already expired. Maybe the following coupon can help you"). '</span>
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
            $siteImage =
                '<img class="logo-img" 
                src="'.HTTP_PATH.'public/images/logo-3.png" width="109" height="49" alt="Flipit" title="Flipit">';
        }
        return $siteImage;
    }

    public static function getVerifiedText()
    {
        $verifiedText = "
            <div class='verified-text'>
                <strong>" . FrontEnd_Helper_viewHelper::__translate('Verified') . "</strong>
                <span class='glyphicon glyphicon-ok'></span>
            </div>";
        return $verifiedText;
    }

    public function getContentManagerName($contentManagerName)
    {
        $explodeContentManagerName = explode(' ', $contentManagerName);
        $contentManagerName = !empty($explodeContentManagerName[0]) ? $explodeContentManagerName[0] : '';
        return $contentManagerName;
    }
}