<?php
class FrontEnd_Helper_PageHeaderPartialFunctions
{
    public function getCategoryOrPageHeader($headerText, $headerImage, $pageType = '')
    {
        $headertitle = '<h1>' . $headerText . '</h1>';
        $header = '<div class="banner-block">
            <img alt="' . $headerText . '" src="' .  $headerImage . '" class="image" title="' . $headerText . '">
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

    public function getTop20Header($headerText, $headerSubText, $headerImage)
    {
        $headertitle = '<h1>' . $headerText . '</h1>';
        $headerSubTitle = '<h2>' . $headerSubText . '</h2>';
        $header = '<div class="header-block top-header-block"> 
            <div class="icon">
                <a href=""><img width="50" height="50" alt="' . $headerText . '" title="' . $headerText . '"  
                    src="' .  $headerImage . '" class="radiusImg"></a>
            </div>
            <div class="box">
                <div class="holder">
                    '.$headertitle.$headerSubTitle.'
                </div>
            </div>
        </div>';
        return $header;
    }
}
