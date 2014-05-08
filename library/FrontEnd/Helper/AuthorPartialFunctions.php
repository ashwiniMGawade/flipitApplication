<?php
class FrontEnd_Helper_AuthorPartialFunctions extends FrontEnd_Helper_viewHelper {
    public static function getShopLogos($shops) {
        $shopLogos = '';
        foreach ($shops as $shop):
            if (!empty($shop['shops'])):
                $shopImage = PUBLIC_PATH_CDN.ltrim($shop['shops'][0]['logo']['path'], '/')."thum_small_".$shop['shops'][0]['logo']['name'];
                $linkToShopDetail = HTTP_PATH_LOCALE.$shop['shops'][0]['permaLink'];
                $shopLogos.=
                '<li>
                    <a href="'. $linkToShopDetail.'"><img src="'.$shopImage.'" width="90" height="45" alt="'.$shop['name'].'"></a>
                </li>';
            endif;
        endforeach;
        return $shopLogos;
    }

    public function authorSocialMediaLinks($authorDetail)
    {
        $authorSocialMediaLinks = $authorDetail['twitter']!="" ? $this->getSocialMediaLink($authorDetail['twitter'], 'twitter.png', 'twitter') : '';
        $authorSocialMediaLinks .= $authorDetail['google']!="" ? $this->getSocialMediaLink($authorDetail['google'], 'plus.png', 'google plus') : '';
        $authorSocialMediaLinks .= $authorDetail['pinterest']!="" ? $this->getSocialMediaLink($authorDetail['pinterest'], 'p-icon.png', 'pinterest') : '';
        return $authorSocialMediaLinks;
    }

    public function getSocialMediaLink($socialMediaLinkUrl, $socialMediaLinkImage, $socialMediaLinkName)
    {
        return '<li>
            <a href="'.$socialMediaLinkUrl.'" target="_blank">
                <img src="' .HTTP_PATH ."public/images/front_end/". $socialMediaLinkImage .'" width="16" height="16" />' . $this->zendTranslate->translate($socialMediaLinkName)
            .'</a>
        </li>';
    }
    
    public function getAuthorCountryName($locale)
    {
        $countryName = '';
        if(!empty($locale)) :
            $locale = new Zend_Locale($locale);
            $countries = $locale->getTranslationList('Territory');
            $countryName = ($countries[$locale->getRegion()]);
        endif;
        return $countryName;
    }
    public function getAuthorCountryFlagImage($authorCountryFlagName)
    {
        $autherCountryFlag = $authorCountryFlagName!='' ? PUBLIC_PATH ."images/front_end/flags/flag_" . $authorCountryFlagName .".jpg" : '';
        return $autherCountryFlag;
    }
    
    public function getAuthorCountryFlagWithCountryName($authorLocale)
    {
        $autherLocaleName = isset($authorLocale) ? $authorLocale : '';
        $splitAutherLocaleName = explode('_' , $autherLocaleName);
        $authorCountryFlagName = isset($splitAutherLocaleName[1]) ? $splitAutherLocaleName[1] : '';
        $authorCountryName = $this->getAuthorCountryName($autherLocaleName);
        $authorFlagImageLi = '';
        if (!empty($authorCountryName)) {
            $authorFlagImageLi = 
                '<li>
                     <img src="'.$this->getAuthorCountryFlagImage($authorCountryFlagName).' " width="16" height="11" alt="'. $authorCountryName.'">
                     <span>' . $authorCountryName .'</span>
                </li>';
        }
        return $authorFlagImageLi;
    }
}
