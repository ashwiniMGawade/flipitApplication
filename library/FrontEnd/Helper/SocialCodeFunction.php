<?php
class FrontEnd_Helper_SocialCodeFunction
{
    public static function validateZendForm($currentSubmittedForm, $form)
    {
        self::checkFormIsValidOrNot($currentSubmittedForm, $form);
    }

    public static function checkFormIsValidOrNot($currentSubmittedForm,	$signUpFormForStorePage)
    {
        if ($currentSubmittedForm->getRequest()->isPost()) {
            if ($signUpFormForStorePage->isValid($currentSubmittedForm->getRequest()->getPost())) {
                
                exit();
            } else {
                $whichFormIsPostForValidation->highlightErrorElements();
            }
        }
        return true;
    }
}
?>