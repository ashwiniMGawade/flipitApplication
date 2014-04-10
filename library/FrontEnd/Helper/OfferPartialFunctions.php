<?php
class FrontEnd_Helper_OfferPartialFunctions extends FrontEnd_Helper_viewHelper
{
    public static function getUrlToShow($currentOffer)
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
       
    }

    public static function getOfferBounceUrl($offerId)
    {
        return HTTP_PATH_LOCALE."out/offer/".$offerId;
    }

    public static function getDiscountImage($currentOffer)
    {
        $offerDiscountImage ='';
        if (!empty ( $currentOffer->tiles)) {
            $offerDiscountImage = PUBLIC_PATH_CDN . ltrim(@$currentOffer->tiles['path'], "/").@$currentOffer->tiles['name'];
        }

        return $offerDiscountImage;
    }

    public static function getOfferTermsAndCondition($currentOffer)
    {
        $termsAndConditions = '';
        if (count($currentOffer->termandcondition) > 0) {
            $termsAndConditions = $currentOffer->termandcondition[0]['content'];
        }

        return $termsAndConditions;
    }

    public static function getDaysDifference($endDate)
    {
        $currentDate = date('Y-m-d');
        $offerEndDate = date('Y-m-d', strtotime($endDate));
        $timeStampStart = strtotime($offerEndDate);
        $timeStampEnd = strtotime($currentDate);

        $dateDifference = abs($timeStampEnd - $timeStampStart);
        $dayDifference = floor($dateDifference/(60*60*24));
    }

    public static function getUserIsLoginOrNot()
    {
        $userLoginStatus = false;
        if (Auth_VisitorAdapter::hasIdentity()) :
        $userLoginStatus = true;
        endif;

        return $userLoginStatus;
    }

    public static function getOfferOption($string)
    {
        $string = '<strong class="exclusive">
        <span class="glyphicon glyphicon-star"></span>'.$string.'</strong>';

        return $string;
    }

    public static function getOfferExclusiveOrEditor($currentOffer)
    {
        $offerOption = '';
        $trans = Zend_Registry::get('Zend_Translate');

        if ($currentOffer->exclusiveCode == '1'):
        $offerOption = self::getOfferOption($trans->translate('Exclusive'));
        elseif ($currentOffer->editorPicks =='1'):
        $offerOption = self::getOfferOption($trans->translate('Editor'));
        endif;

        return $offerOption;
    }

    public static function dateStringFormat($currentOffer, $dayDifference)
    {
        $trans = Zend_Registry::get('Zend_Translate');

        $offerOption = self::getOfferExclusiveOrEditor($currentOffer);

        $stringAdded = $trans->translate('Added');
        $stringOnly = $trans->translate('Only');
        $startDate = new Zend_Date(strtotime($currentOffer->startDate));

        $dateFormatString = '';
        if($currentOffer->discountType == "CD"):
        $dateFormatString .= $stringAdded;
        $dateFormatString .= ':';
        $dateFormatString .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
        $dateFormatString .= ',';

        if ($dayDifference ==5 || $dayDifference ==4 || $dayDifference ==3 || $dayDifference ==2) {
            $dateFormatString .= $stringOnly;
            $dateFormatString .= '&nbsp;';
            $dateFormatString .= $dayDifference;
            $dateFormatString .= '&nbsp;';
            $dateFormatString .= $trans->translate('days valid!');

        } elseif ($dayDifference ==1) {
            $dateFormatString .= $stringOnly;
            $dateFormatString .= '&nbsp;';
            $dateFormatString .= $dayDifference;
            $dateFormatString .= '&nbsp;';
            $dateFormatString .= $trans->translate('day only!');

        } elseif ($dayDifference ==0) {
            $dateFormatString .= $trans->translate('Expires today');

        } else {
            $endDate = new Zend_Date(strtotime($this->endDate));
            $dateFormatString .= $trans->translate('Expires on').':';
            $dateFormatString .= ucwords($endDate->get(Zend_Date::DATE_MEDIUM));
        } elseif ($currentOffer->discountType == "PR" || $currentOffer->discountType == "SL" || $currentOffer->discountType == "PA"):
        $dateFormatString .= $stringAdded;
        $dateFormatString .= ':';
        $dateFormatString .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
        endif;

        return $offerOption . $dateFormatString;
    }

    public static function getClassNameForOffer($currentOffer)
    {
        $className = 'code';
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $className .= ' purple';
        } elseif ($currentOffer->discountType=='SL') {
            $className .= ' red';
        } elseif ($currentOffer->extendedOffer =='1') {
            $className .= ' blue';
        }
        return $className;
    }
    
    public static function getOfferImage($currentOffer, $offersType)
    {
       $offerImageDiv ='';
       if($offersType=='simple')
       {
          $offerDiscountImage = self::getDiscountImage($currentOffer);
          $altAttributeText = @$currentOffer->tiles['label'];
          $offerImageDiv = self::getImageTag($offerDiscountImage, $altAttributeText);
       } else {
           $offerDiscountImage = self::getShopLogoForOffer($currentOffer);
           $altAttributeText = $currentOffer->shop['name'];
           $imageTag = self::getImageTag($offerDiscountImage, $altAttributeText);
           $offerImageDiv = $imageTag . '<footer class="bottom">' . self::getOfferFooterText($currentOffer) . '</footer>';
       }
       return $offerImageDiv;
    }
    
    public static function getShopLogoForOffer($currentOffer)
    {
        return PUBLIC_PATH_CDN.ltrim($currentOffer->shop['logo']['path'], "/").'thum_small_'. $currentOffer->shop['logo']['name'];
    }
    
    public static function getImageTag($offerDiscountImage, $altAttributeText){
        return '<img src="'.$offerDiscountImage.'" alt="'.$altAttributeText.'"/>';
    }
    
    public static function getOfferFooterText($currentOffer)
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $className = 'code';
        if ($currentOffer->discountType == "PR" || $currentOffer->discountType == "PA") {
            $className .= $trans->translate('printable');
        } elseif ($currentOffer->discountType=='SL') {
            $className .= $trans->translate('sale');
        } elseif ($currentOffer->extendedOffer =='1') {
            $className .= $trans->translate('deal');
        }
        return $className;
    }
    
    public static function getSectionHeader($shopName, $offersType)
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $SimialrShopHeader = '';
        if ($offersType=='similar') {
            $SimialrShopHeader = '<header class="heading-box text-coupon">
            <h2>'.$trans->translate('Coupon codes for similar stores').'</h2>
            <strong>'.$trans->translate('Similar vouchers and discounts for'). ' ' . $shopName .'</strong>
            </header>';
        }
        return $SimialrShopHeader;
    }
    
    public static function getShopLogoForSignUp($shop)
    {
    	$imgTagWithImage = '';
        if($shop!=null){
           $shopLogoImage = PUBLIC_PATH_CDN.ltrim($shop['logo']['path'], "/").'thum_medium_'. $shop['logo']['name'];
           $imgTagWithImage = '<img alt="' . $shop['logo']['name']. '" src="'. $shopLogoImage .'">';
        } else {
           $imgTagWithImage = '<div class="ico-mail"></div>';
        }
        return $imgTagWithImage;
    }
}
