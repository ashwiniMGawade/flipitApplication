<?php
class FrontEnd_Helper_PageHeaderPartialFunctions
{
    public function getCategoryOrPageHeader($headerText, $headerImage)
    {
        $header = '<div class="banner-block">
            <img alt="' . $headerText . '" src="' .  $headerImage . '" class="image">
            <div class="bar">
            <span>' . $headerText . '</span></div>
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
