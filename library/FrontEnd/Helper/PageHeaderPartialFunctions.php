<?php
class FrontEnd_Helper_PageHeaderPartialFunctions extends FrontEnd_Helper_viewHelper {
    public function getCategoryOrPageHeader($headerText,  $headerImage) {
        $header = '<div class="banner-block">
            <img alt="' . $headerText . '" src="' .  $headerImage . '" class="image">
            <div class="bar">
            <span>' . $headerText . '</span></div>
            </div>';
        return $header;
    }
}
