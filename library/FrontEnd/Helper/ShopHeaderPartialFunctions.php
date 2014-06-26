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
        $divContent =
            '<div class="header-block header-block-2">
                <div id="messageDiv" class="yellow-box-error-box-code" style="margin-top : 20px; display:none;">
                    <strong></strong>
                </div>
                <div class="icon">
                    <a target="_blank" rel="nofollow" 
                    class="text-blue-link store-header-link '.$affliateClass.'"  '.$affliateDisabled.'
                    onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">
                    <img class="radiusImg" 
                    src="'. PUBLIC_PATH_CDN . $shop['logo']['path'] . $shop['logo']['name']. '" 
                    alt="'.$shop['name'].'" width="176" height="89" />
                    </a>
                </div>
            <div class="box">';
        if ($expiredMessage !='storeDetail') {
            $shop['subTitle'] = $this->__translate('Expired').' '.$shop['name'].' '.$this->__translate('copuon code');
        } else {
            $shop['subTitle'] = $shop['subTitle'];
        }
        if ($expiredMessage !='') {
                $divContent .=
                    '<h1>'.$shop['title'].'</h1>
                    <strong>'.$shop['subTitle'].'</strong>
                        <a target="_blank" rel="nofollow" 
                        class="btn text-blue-link fl store-header-link '.$affliateClass.' pop btn btn-sm btn-default" '
                        .$affliateDisabled.'
                        onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">'.$shop['actualUrl'].'
                        </a>'. self::getLoveAnchor($shop['id']);
        } else {
            $divContent .='<h1>'.$offerTitle.'</h1>';
        }
            $divContent .='</div></div>';
        return $divContent;
    }
    
    public function getLoveAnchor($shopId)
    {
        $visitorId = 0;
        if(Auth_VisitorAdapter::hasIdentity()):
            $visitorId = Auth_VisitorAdapter::getIdentity()->id;
        endif;
        $loveClassGreyColorOrRedColor = 'glyphicon glyphicon-heart red-heart';
        if (Visitor::getFavoriteShopsForUser($visitorId, $shopId)==false):
            $loveClassGreyColorOrRedColor = 'glyphicon glyphicon-heart';
        endif;
        return '<a class="pop btn btn-sm btn-default" href="javascript:void(0)">
            <span class="' . $loveClassGreyColorOrRedColor . '"></span>'.
            $this->__translate('Love').
        '</a>';
    }
}
