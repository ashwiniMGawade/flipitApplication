<?php
class FrontEnd_Helper_LandingPageFunctions extends FrontEnd_Helper_viewHelper
{
    public function getShopHeader($shop)
    {
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $html = '<div class="logo-shop">';
        if ($shop->getAffliateProgram()) {
            $shopUrl = $domainName . 'out/shop/' . $shop->getId();
            $html .= '<a href="' . $shopUrl . '" target="_blank">';
        } else {
            $html .= '<a href="#">';
        }
        $html .= '<img src="'.PUBLIC_PATH_CDN.$shop->getLogo()->path.'thum_big_'.$shop->getLogo()->name.'" alt="zalando" width="114" height="53">';
        $html .= '</a></div>';
        return $html;
    }

    public function getDaysTillExpire($daysTillOfferExpires)
    {
        $daysTillOfferExpires = intval($daysTillOfferExpires);
        $onlyDaysString = FrontEnd_Helper_viewHelper::__translate('Only').' '. $daysTillOfferExpires.' ';
        $onlyDaysLeftString = '';
        if ($daysTillOfferExpires == 3 || $daysTillOfferExpires == 2) {
            $onlyDaysLeftString = $onlyDaysString. FrontEnd_Helper_viewHelper::__translate('days left!');
        } elseif ($daysTillOfferExpires == 1) {
            $onlyDaysLeftString = $onlyDaysString. FrontEnd_Helper_viewHelper::__translate('day left!');
        } elseif ($daysTillOfferExpires == 0) {
            $onlyDaysLeftString = FrontEnd_Helper_viewHelper::__translate('Expires today');
        }
        if (empty($onlyDaysLeftString)) {
            $onlyDaysLeftString = '';
        } else {
            $onlyDaysLeftString = '<span class="validity"><span class="icon-timer"></span>'. $onlyDaysLeftString
                . '</span>';
        }
        return  $onlyDaysLeftString;
    }

    public function getOfferTermsAndCondition($currentOffer)
    {
        $termsAndConditions = '';
        if (isset($currentOffer->offertermandcondition)) {
            if (count($currentOffer->offertermandcondition) > 0) {
                $termsAndConditions = $currentOffer['offertermandcondition'][0]['content'];
            }
        }
        return $termsAndConditions;
    }
}
