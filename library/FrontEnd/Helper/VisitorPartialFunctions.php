<?php
class FrontEnd_Helper_VisitorPartialFunctions extends FrontEnd_Helper_viewHelper {
    public static function getTestimonialStaticImage() {
        $testimonialHtml = 
            '<div class="image">
                <img width="462" height="263" alt="register Image"
                src="'. PUBLIC_PATH .'"images/img-36.png">
                <div class="text">'. $this->translate('Flipit! Get ready to flip over a great deal!') .'
                </div>
            </div>';
        }
}
