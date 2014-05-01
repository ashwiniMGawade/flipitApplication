<?php
class FrontEnd_Helper_CategoryPartialFunctions extends FrontEnd_Helper_viewHelper {

    public function getCategoryOrSpecialPageHeder($categoryName,  $categoryFeaturedImage) {
        $categoryPageHeader = '<div class="banner-block">
            <img alt="' . $categoryName . '" src="' .  $categoryFeaturedImage . '" class="image">
            <div class="bar">
            <span>' . $categoryName . '</span></div>
            </div>';
        return $categoryPageHeader;
    }
}
