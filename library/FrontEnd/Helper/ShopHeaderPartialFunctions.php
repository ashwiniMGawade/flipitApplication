<?php
class FrontEnd_Helper_ShopHeaderPartialFunctions extends FrontEnd_Helper_viewHelper
{
    public function getShopHeader($shop, $expiredMessage, $offerTitle)
    {
        $bounceRate = "/out/shop/".$shop['id'];
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $shopUrl = $domainName.'out/shop/'.$shop['id'];
        $affliateProgramUrl = $shop['affliateProgram'] =='' ? $shop['actualUrl'] : $shop['affliateProgram'];
        if ($shop['affliateProgram']) :
            $affliateBounceRate = "ga('send', 'event', 'aff','$bounceRate');";
            $affliateUrl = $shopUrl;
            $affliateDisabled = '';
            $affliateClass = '';
        else:
            $affliateBounceRate = '';
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
            alt="'.$shop['name'].'" width="176" height="89" />';
        $shopImageContent = $affliateUrl != '#' ? '<a target="_blank" rel="nofollow" 
            class="text-blue-link store-header-link '.$affliateClass.'"  '.$affliateDisabled.'
            onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">'.$shopImage.'</a>' : $shopImage;
        $divContent =
            '<div class="header-block header-block-2">
                <div id="messageDiv" class="yellow-box-error-box-code" style="margin-top : 20px; display:none;">
                    <strong></strong>
                </div>
                <div class="icon">
                '.$shopImageContent.'
                </div>
            <div class="box">';

        if ($expiredMessage !='storeDetail') {
            $shop['subTitle'] = $this->__translate('Expired').' '.$shop['name'].' '.$this->__translate('copuon code');
        } else {
            $shop['subTitle'] = $shop['subTitle'];
        }

        if ($expiredMessage !='') {
            $explodedShopUrl = explode('//', $shop['actualUrl']);
            $shopWebsiteUrl = isset($explodedShopUrl[1]) ? $explodedShopUrl[1] : $explodedShopUrl[0];
            $shopWebsiteUrlContent = $affliateUrl != '#' ? '<a target="_blank" rel="nofollow" 
                class="btn text-blue-link fl store-header-link '.$affliateClass.' pop btn btn-sm btn-default" '
                .$affliateDisabled.'
                onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">'.$shopWebsiteUrl.'
                </a>' : '<span disabled="disabled"
                class="btn text-blue-link fl store-header-link pop btn btn-sm btn-default btn-disabled">'.
                $shopWebsiteUrl.'</span>';
            $divContent .=
                '<h1>'.FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']).'</h1>
                <h2>'.FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']).'</h2>
                    '.$shopWebsiteUrlContent . self::getLoveAnchor($shop['id'], $shop['name'], $shop['permaLink']);
        } else {
            $divContent .='<h1>'.$offerTitle.'</h1>';
        }
        
        $divContent .='</div></div>';
        return $divContent;
    }
    
    public function getLoveAnchor($shopId, $shopName, $shopPermalink)
    {
        $visitorId = Auth_VisitorAdapter::hasIdentity() ? Auth_VisitorAdapter::getIdentity()->id : 0;
        $redirectUrl = HTTP_PATH_LOCALE. 'store/addtofavourite?permalink='. $shopPermalink .'&shopId='
            . base64_encode($shopId);
        $titleTextForLove = $this->__form('form_Remove from Favourite');
        $emAndSpan = '<em>'.$this->__translate("I FOLLOW THIS").'</em>
            <strong>'.$this->__translate("REMOVE").'</strong>';
        $aTagClasses = 'pop btn btn-sm btn-default remove-fovourite blue';
        if (Visitor::getFavoriteShopsForUser($visitorId, $shopId)==false):
            $titleTextForLove = $this->__form("form_Add in Favourite");
            $emAndSpan = '<span class="glyphicon red-heart glyphicon-heart"></span>
                <span class = "follow-text">'.$this->__translate("FOLLOW").'</span>';
            $aTagClasses = 'pop btn btn-sm btn-default follow-button';
        endif;

        $clickEvent = '';
        if ($visitorId==0) {
            $redirectUrl = 'javascript:void(0)';
            $clickEvent = "onclick=showSignUpPopUp();";
        }
        return '<a title="'. $titleTextForLove .'" '.$clickEvent.' href="' . $redirectUrl .'" 
            class="'. $aTagClasses . '">'. $emAndSpan.
        '</a>';
    }
}
