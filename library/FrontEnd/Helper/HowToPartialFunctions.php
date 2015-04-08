<?php
class FrontEnd_Helper_HowToPartialFunctions
{
    public static function getHowToSubSubTitleWithFallBack($shopName, $howToGuides)
    {
        $howToSubSubTitle = str_replace('[shop]', $shopName, $howToGuides[0]['howtoSubSubTitle']);
        if (empty($howToSubSubTitle)) {
            $howToSubSubTitle = str_replace('[shop]', $shopName, $howToGuides[0]['howtoTitle']);
        }
        return $howToSubSubTitle;
    }
}
