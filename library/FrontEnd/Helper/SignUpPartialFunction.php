<?php
class FrontEnd_Helper_SignUpPartialFunction extends FrontEnd_Helper_viewHelper
{
    public static function validateZendForm($currentSumittedForm, $singUpFormForStorePage, $signUpFormSideBarWidget)
    {
        self::checkFormIsValidOrNot($currentSumittedForm, $singUpFormForStorePage, $signUpFormSideBarWidget);
    }
    public static function createFormForSignUp($formName, $submitButtonLabel)
    {
        return new Application_Form_SignUp($formName, $submitButtonLabel);
    }

    public static function checkFormIsValidOrNot($thisSignUpNewsLetterform, $singUpFormForStorePage, $signUpFormSideBarWidget)
    {
        if ($thisSignUpNewsLetterform->getRequest()->isPost()) {
            $whichFormIsValidate = $thisSignUpNewsLetterform->getRequest()->getParam('SignUp')=='SignUp' ? $singUpFormForStorePage : $signUpFormSideBarWidget;
            if ($whichFormIsValidate->isValid($thisSignUpNewsLetterform->getRequest()->getPost())) {
                $signUpStep2Url = self::singUp2RedirectLink($whichFormIsValidate);
                header('location:'. $signUpStep2Url);
            } else {
                $whichFormIsValidate->highlightErrorElements();
            }
        }
        return true;
    }

    public static function singUp2RedirectLink($signUpNewsLetterform)
    {
        $emailAddress = $signUpNewsLetterform->getValue('emailAddress');
        $addToFavoriteShopId = $signUpNewsLetterform->getValue('shopId');
        $signUpStep2Url= HTTP_PATH_LOCALE.self::__link('inschrijven') . '/' . self::__link('stap2') . '/' . base64_encode($emailAddress);
        return $signUpStep2Url;
    }
}
?>