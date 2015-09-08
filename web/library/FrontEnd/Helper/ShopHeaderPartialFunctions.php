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
        if('se' == LOCALE) {
            $affliateBounceRate .= "gtmDataBuilder('retailerClickout', 'Retailer', 'Retailer Logo', 'Retailer', ".$shop['id'].");";
        }
        if ($shop['affliateProgram']) :
            $affliateBounceRate .= "ga('send', 'event', 'aff','$bounceRate');";
            $affliateUrl = $shopUrl;
            $affliateDisabled = '';
            $affliateClass = '';
        else:
            $affliateBounceRate .= '';
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
            onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">'.$shopImage.'</a>' : $shopImage;
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
}
