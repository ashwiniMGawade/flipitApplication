<?php
class FrontEnd_Helper_CategoryPartialFunctions extends FrontEnd_Helper_viewHelper {
    public function getCategoryHeader($categoryName, $categoryFuturedImage) {
        $categoryFuturedImage = PUBLIC_PATH . "images/banner-03.jpg";
        $categoryPageHeader = '<div class="banner-block">
            <img alt="' . $categoryName . '" src="' . $categoryFuturedImage . '" class="image">
            <div class="bar">
            <span>' . $categoryName . '</span></div>
            </div>';
        return $categoryPageHeader;
    }
}
