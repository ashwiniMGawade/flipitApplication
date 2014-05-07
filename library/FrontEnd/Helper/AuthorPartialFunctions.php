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
}
