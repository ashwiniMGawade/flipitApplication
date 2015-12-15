<?php
class FrontEnd_Helper_ShopHeaderPartialFunctions extends FrontEnd_Helper_viewHelper
{
    public function getShopHeader($shop, $expiredMessage, $offerTitle)
    {
        $affliateBounceRate = '';
        $bounceRate = "/out/shop/".$shop['id'];
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $shopUrl = $domainName.'out/shop/'.$shop['id'];
        $affliateProgramUrl = $shop['affliateProgram'] =='' ? $shop['actualUrl'] : $shop['affliateProgram'];
        $gtmData = array(
            'event' => 'retailerClickout',
            'variant' => 'Retailer',
            'clickedElement' => 'Retailer Logo',
            'retailerId' => $shop['id']
        );
        $affliateBounceRate .= "gtmDataBuilder(".json_encode($gtmData).");";
        if ($shop['affliateProgram']) :
            $affliateUrl = $shopUrl;
            $affliateDisabled = '';
            $affliateClass = '';
        else:
            $affliateUrl = '#';
            $affliateDisabled = 'disabled="disabled"';
            $affliateClass = 'btn-disabled';
        endif;
        return self::getHeaderBlockContent(
            $affliateBounceRate,
            $affliateUrl,
            $affliateDisabled,
            $affliateClass,
            $shop,
            $expiredMessage,
            $offerTitle
        );
    }

    public function getHeaderBlockContent(
        $affliateBounceRate,
        $affliateUrl,
        $affliateDisabled,
        $affliateClass,
        $shop,
        $expiredMessage,
        $offerTitle
    ) {
        $shopImage = '<img class="radiusImg" 
            src="'. PUBLIC_PATH_CDN . $shop['logo']['path'] . "thum_big_" . $shop['logo']['name']. '" 
            alt="'.$shop['name'].'" width="176" height="89" title="'.$shop['name'].'" />';
        $shopImageContent = $affliateUrl != '#' ? '<a target="_blank" rel="nofollow" 
            class="text-blue-link store-header-link '.$affliateClass.'"  '.$affliateDisabled.'
            onclick=\''.$affliateBounceRate.'\' href="'.$affliateUrl.'">'.$shopImage.'</a>' : $shopImage;
        $divContent =
            '<div class="header-block header-block-2">
                <div id="messageDiv" class="yellow-box-error-box-code" style="margin-top : 20px; display:none;">
                    <span class="glyphicon glyphicon-warning-sign"></span>
                    <strong></strong>
                </div>
                <div class="icon">
                '.$shopImageContent.'
                </div>
            <div class="box"><div class="holder">';

        if ($expiredMessage =='storeDetail') {
            $shop['subTitle'] = $shop['subTitle'];
        } else if ($expiredMessage =='storeHowTo') {
            if (!empty($shop['howtoTitle'])) {
                $shop['title'] = str_replace('[shop]', $shop['name'], $shop['howtoTitle']);
            }
            if (!empty($shop['howtoSubtitle'])) {
                $shop['subTitle'] = str_replace('[shop]', $shop['name'], $shop['howtoSubtitle']);
            }
        } else {
            $shop['subTitle'] = $this->__translate('Expired').' '.$shop['name'].' '.$this->__translate('copuon code');
        }

        if ($expiredMessage !='') {           
            $divContent .=
                '<h1>'.FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']).'</h1>
                <h2>'.FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']).'</h2>
                    ';
        } else {
            $divContent .='<h1>'.$offerTitle.'</h1>';
        }
        return $divContent;
    }

    public function getDisqusReplyCounter($shop)
    {
        $anchorTag = '';
        if (isset($shop['permaLink'])) {
            $shopPermalink = $shop['permaLink'];
            $disqusUrl = HTTP_PATH_LOCALE.$shopPermalink;
            $anchorTag =
                '<a id="commentCount" href="javascript:void(0);" onClick="scrollToDisqus();" 
                    class="btn text-blue-link fl store-header-link  pop btn btn-sm btn-default follow-button" 
                    rel="nofollow"><span class="icon-chat"></span>
                    <span id="commentCountSpan" 
                    class="disqus-comment-count follow-text" data-disqus-url="'.$disqusUrl.'"> </span>
                </a>';
        }
        
        return $anchorTag;
    }

    public function getFloatingCoupon()
    {
        if (isset($_COOKIE['floatingCouponClosed']) && !empty($_COOKIE['floatingCouponClosed'])) {
            return '';
        }
        $showFloatingCouponSetting = \FrontEnd_Helper_viewHelper::getSetting('SHOW_FLOATING_COUPON');
        if ($showFloatingCouponSetting instanceof \Core\Service\Errors || 0 == (int) $showFloatingCouponSetting->getValue()) {
            return '';
        }
        $offer = KC\Repository\Offer::getFloatingCoupon();
        if (true === empty($offer)) {
            return '';
        }
        $offer = (object)$offer[0];
        $offerPartial = new FrontEnd_Helper_OffersPartialFunctions();
        $urlToShow = $offerPartial->getUrlToShow($offer);
        $popupLink = $offerPartial->getPopupLink($offer, $urlToShow);
        $onClick = "OpenInNewTab(\"".HTTP_PATH_LOCALE.$offer->shopOffers['permaLink'].$popupLink."\");";

        $gtmData = array(
            'event' => 'voucherClickout',
            'variant' => 'Code',
            'clickedElement' => 'Text Link',
            'offerId' => $offer->id,
            'isExpired' => isset($offer->expiredOffer) ? 'True' : 'False',
            'isFloating' => 'True'
        );
        $gtmData = json_encode($gtmData);
        $gtmClick = "gtmDataBuilder($gtmData);";
        $offerDaysLeft = $offer->endDate->diff(new \DateTime())->days;
        $dayLeftText =  ( $offerDaysLeft < 1 ? 'Expires Today' : ( ( $offerDaysLeft > 1 ) ? $offerDaysLeft . ' days left' : '1 day left' ) );
        $floatingCoupon = '<div class="popup-box hide floating-coupon-box" id="floatingCouponBox" offerId="'.$offer->id.'">
                <a href="#" class="btn-close">close</a>
                <span class="time">clock</span>
                <span class="text">' . $this->__translate( 'Tip : ' . $dayLeftText . ' - Discount at ' ) . $offer->shopOffers['name'] . $this->__translate(' with this code.').'
                <a href="'.$urlToShow.'" onclick=\'setFloatingCouponCookie();'.$gtmClick.$onClick.'\' class="floating-coupon-link">'.$this->__translate('Click here for the coupon code').'</a></span>
            </div>';
        return $floatingCoupon;
    }
}
