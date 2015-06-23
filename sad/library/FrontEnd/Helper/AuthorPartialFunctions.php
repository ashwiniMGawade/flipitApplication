<?php
class FrontEnd_Helper_AuthorPartialFunctions
{
    public static function getShopLogos($shops)
    {
        $shopLogos = '';
        foreach ($shops as $shop):
            if (!empty($shop['shops'])):
                $shopImage =
                    PUBLIC_PATH_CDN.ltrim($shop['shops'][0]['logo']['path'], '/')
                    ."thum_small_".$shop['shops'][0]['logo']['name'];
                $linkToShopDetails = HTTP_PATH_LOCALE.$shop['shops'][0]['permaLink'];
                $shopLogos.=
                '<li>
                    <a href="'. $linkToShopDetails.'">
                        <img src="'.$shopImage.'" width="90" height="45" alt="'.$shop['name'].'" title="'.$shop['name'].'">
                    </a>
                </li>';
            endif;
        endforeach;
        return $shopLogos;
    }

    public function authorSocialMediaLinks($authorDetails)
    {
        $authorSocialMediaLinks =
            $authorDetails['twitter']!=""
            ? $this->getSocialMediaLink($authorDetails['twitter'], 'ico-04.png', 'twitter')
            : '';
        $authorSocialMediaLinks .=
            $authorDetails['google']!="" ?
            $this->getSocialMediaLink($authorDetails['google'], 'plus.png', 'google plus')
            : '';
        $authorSocialMediaLinks .=
            $authorDetails['pinterest']!=""
            ? $this->getSocialMediaLink($authorDetails['pinterest'], 'p-icon.png', 'pinterest')
            : '';
        return $authorSocialMediaLinks;
    }

    public function getSocialMediaLink($socialMediaLinkUrl, $socialMediaLinkImage, $socialMediaLinkName)
    {
        return
        '<li>
            <a href="'.$socialMediaLinkUrl.'" target="_blank">
                <img src="' .HTTP_PATH ."public/images/front_end/"
                . $socialMediaLinkImage .'" width="16" height="16" title="Social Media Image" />' .$socialMediaLinkName
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
        $authorCountryFlag =
            $authorCountryFlagName!=''
            ? PUBLIC_PATH ."images/front_end/flags/flag_" . $authorCountryFlagName .".jpg"
            : '';
        return $authorCountryFlag;
    }
    
    public function getAuthorCountryFlagWithCountryName($authorLocale)
    {
        $authorLocaleName = isset($authorLocale) ? $authorLocale : '';
        $splitAuthorLocaleName = explode('_', $authorLocaleName);
        $authorCountryFlagName = isset($splitAuthorLocaleName[1]) ? $splitAuthorLocaleName[1] : '';
        $authorCountryName = $this->getAuthorCountryName($authorLocaleName);
        $authorFlagImageLi = '';
        if (!empty($authorCountryName)) {
            $authorFlagImageLi =
                '<li>
                    <span class="country-flags '.strtolower($splitAuthorLocaleName[1]).'"></span>
                    <span>' . $authorCountryName .'</span>
                </li>';
        }
        return $authorFlagImageLi;
    }

    public static function getAuthorName($firstName, $lastName)
    {
        return $authorName = $firstName;
    }
}
