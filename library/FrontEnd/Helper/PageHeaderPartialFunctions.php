<?php
class FrontEnd_Helper_PageHeaderPartialFunctions
{
    public function getCategoryOrPageHeader($headerText, $headerImage, $pageType = '')
    {
        if ($pageType == 'info') {
            $headertitle = '<h1>' . $headerText . '</h1>';
        } else {
            $headertitle = '<span>' . $headerText . '</span>';
        }

        $header = '<div class="banner-block">
            <img alt="' . $headerText . '" src="' .  $headerImage . '" class="image">
            <div class="bar">' . $headertitle . '</div>
            </div>';
        return $header;
    }
    public static function getCategoryOrPageHeaderImage($headerImage)
    {
        $imagePath = isset($headerImage['path']) ? $headerImage['path'] : '';
        $imageName = isset($headerImage['name']) ? $headerImage['name'] : '';
        return PUBLIC_PATH_CDN. $imagePath . $imageName;
    }
}