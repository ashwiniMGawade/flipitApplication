<?php
class FrontEnd_Helper_LandingPageFunctions extends FrontEnd_Helper_viewHelper
{
    public function getShopHeader($shop)
    {
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $html = '<div class="logo-shop">';
        if ($shop->getAffliateProgram()) {
            $shopUrl = $domainName . 'out/shop/' . $shop->getId();
            $html .= '<a href="' . $shopUrl . '">';
        } else {
            $html .= '<a href="#">';
        }
        $html .= '<img src="'.PUBLIC_PATH_CDN.$shop->getLogo()->path.'thum_big_'.$shop->getLogo()->name.'" alt="zalando" width="114" height="53">';
        $html .= '</a></div>';
        return $html;
    }
}
