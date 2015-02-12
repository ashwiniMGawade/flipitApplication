<?php
class FrontEnd_Helper_HowToPartialFunctions
{
    public static function getHowSubSubTitleWithFallBack($shopName, $howToGuides)
    {
        $howSubSubTitle = str_replace('[shop]', $shopName, $howToGuides[0]['howtoSubSubTitle']);
        if (empty($howSubSubTitle)) {
            $howSubSubTitle = str_replace('[shop]', $shopName, $howToGuides[0]['howtoTitle']);
        }
        return $howSubSubTitle;
    }
}
