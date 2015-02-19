<?php
class FrontEnd_Helper_ExpiredOffersPartialFunction{
    
    public function getExpiredOfferUrl($offer)
    {
        if($offer->extendedOffer == 1):
            $expiredOfferUrl =
               HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_deals').'/'.$offer->extendedUrl;
        else:
            $expiredOfferUrl = HTTP_PATH_LOCALE."out/exoffer/".$offer->id;
        endif;
        return $expiredOfferUrl;
    }
    
    public function getOfferTitle($offerTitle)
    {
        if ($offerTitle!=null && $offerTitle!=''):
            $expiredOfferTitle = $offerTitle;
            if (strlen($offerTitle) > 60):
                $expiredOfferTitle = substr($offerTitle, 0, 60) . '...';
            endif;
        endif;
        return $expiredOfferTitle;
    }
    
    public function getOfferCouponCode($offerCouponCode)
    {
        $OfferSpan = '';
        if ($offerCouponCode!=null && $offerCouponCode!=''):
            $expiredOfferCouponCode = $offerCouponCode;
            if (strlen($offerCouponCode) > 40):
                $expiredOfferCouponCode = substr($offerCouponCode, 0, 40) . '...';
            endif;
                $OfferSpan = '<span class="mark"><s>' . $expiredOfferCouponCode. '</s></span>';
        endif;
        return $OfferSpan;
    }
    
    public function getExpiredOnDate($offerEndDate)
    {
        $offerExpiredOn = '';
        $offerExpiredOn =  FrontEnd_Helper_viewHelper::__translate('Expired on:');
        $expiredOfferDate = new Zend_Date(strtotime($offerEndDate));
        $offerExpiredOn = $offerExpiredOn.$expiredOfferDate->get(Zend_Date::DATE_LONG);
        return $offerExpiredOn;
    }
    
    public function getExpiredOffersHeader($shopName)
    {
        $offerHeader = '';
        $offerHeader = '<header class="heading-box text-expired">
        <h2>'
        . FrontEnd_Helper_viewHelper::__translate('Expired').' '. $shopName .' '
        .FrontEnd_Helper_viewHelper::__translate("vouchers and discounts")
        . '</h2>
        <strong>'
        . FrontEnd_Helper_viewHelper::__translate('Unfortunately ... These discount codes from').' '.$shopName.' '
        .FrontEnd_Helper_viewHelper::__translate("you’ve missed") .'</strong>
        </header>';
        return $offerHeader;
    }
}
