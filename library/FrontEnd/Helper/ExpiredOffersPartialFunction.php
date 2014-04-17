<?php
class FrontEnd_Helper_ExpiredOffersPartialFunction extends FrontEnd_Helper_viewHelper{
    
    public static function getExpiredOfferUrl($offer) {
    	if($offer->extendedOffer == 1):
    	    $expiredOfferUrl = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('deals').'/'.$offer->extendedUrl;
    	else:
    	    $expiredOfferUrl = HTTP_PATH_LOCALE."out/exoffer/".$offer->id;
    	endif;
    	return $expiredOfferUrl;
    }
    
    public static function getOfferTitle($offerTitle) {
    	if ($offerTitle!=null && $offerTitle!=''):
    	    $expiredOfferTitle = $offerTitle;
    	    if (strlen($offerTitle) > 60):
    	       $expiredOfferTitle = substr($offerTitle, 0, 60) . '...';
    	    endif;
    	endif;
    	return $expiredOfferTitle;
    }
    
    public static function getOfferCouponCode($offerCouponCode) {
    	$OfferSpan = '';
    	if ($offerCouponCode!=null && $offerCouponCode!=''):
    	    $expiredOfferCouponCode = $offerCouponCode;
	    	if (strlen($offerCouponCode) > 40):
	    	    $expiredOfferCouponCode = substr($offerCouponCode, 0, 40) . '...';
	    	endif;
    			$OfferSpan = '<span class="mark">' . $expiredOfferCouponCode. '</span>';
    	    endif;
    	return $OfferSpan;
    }
    
    public static function getExpiredOnDate($offerEndDate) {
    	$expiredOn = '';
    	$zendTranslate = Zend_Registry::get('Zend_Translate');
    	$expiredOn =  $zendTranslate->translate('Expired on:');
    	$expiredOfferDate = new Zend_Date($offerEndDate);
    	$expiredOn = $expiredOn.ucwords($expiredOfferDate->get(Zend_Date::DATE_MEDIUM));
    	return $expiredOn;
    }
    
    public static function getExpiredOffersHeader($shopName) {
    	$zendTranslate = Zend_Registry::get('Zend_Translate');
        $offerHeader = '';
    	$offerHeader = '<header class="heading-box text-expired">
    	<h2>' . $zendTranslate->translate('Expired').' '. $shopName .' '.$zendTranslate->translate("vouchers and discounts"). '</h2>
    	<strong>'. $zendTranslate->translate('Unfortunately ... These discount codes from').' '.$shopName.' '.$zendTranslate->translate("you’ve missed") .'</strong>
    	</header>';
    	return $offerHeader;
    }
}

?>