<?php
class FrontEnd_Helper_SignUpPartialFunction extends FrontEnd_Helper_viewHelper
{
    public static function validateZendForm($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget)
    {
        self::checkFormIsValidOrNot($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget);
    }
    public static function createFormForSignUp($formName, $submitButtonLabel)
    {
        return new Application_Form_SignUp($formName, $submitButtonLabel);
    }

    public static function checkFormIsValidOrNot($currentSubmittedForm, $signUpFormForStorePage, $signUpFormSidebarWidget)
    {
        if ($currentSubmittedForm->getRequest()->isPost()) {
            $whichFormIsPostForValidation = $currentSubmittedForm->getRequest()->getParam('SignUp')=='SignUp' ? $signUpFormForStorePage : $signUpFormSidebarWidget;
            if ($whichFormIsPostForValidation->isValid($currentSubmittedForm->getRequest()->getPost())) {
                $signUpStep2Url = self::signUp2RedirectLink($whichFormIsPostForValidation);
                header('location:'. $signUpStep2Url);
            } else {
                $whichFormIsPostForValidation->highlightErrorElements();
            }
        }
        return true;
    }

    public static function signUp2RedirectLink($signUpNewsLetterform)
    {
        $emailAddress = $signUpNewsLetterform->getValue('emailAddress');
        $addToFavoriteShopId = $signUpNewsLetterform->getValue('shopId');
        $signUpStep2Url= HTTP_PATH_LOCALE.self::__link('inschrijven') . '/' . self::__link('stap2') . '/' . base64_encode($emailAddress);
        return $signUpStep2Url;
    }

    public function getSignUpWidgetHeader($widgetType)
    {
       if ($widgetType== 'sidebarWidget') {
        $signUpHeader='<h2 class="form-signin-heading">' .$this->zendTranslate->translate('Sign up').'
            <span>'.$this->zendTranslate->translate('and join over').'<br>' .$this->zendTranslate->translate('10 million people')
            .'</span></h2>';
       } else if($widgetType == 'categoryPageSignupForm') {
        $signUpHeader='<h2 class="form-signin-heading">' .'
            <span>'.$this->zendTranslate->translate('Sign up for our newsletter and receive discounts inside your mailbox').'!'
            .'</span></h2>';
       }
       else {
         $signUpHeader='<h2>'.$this->zendTranslate->translate('Receive weekly updates of the best offers?').'<br>'
         .$this->zendTranslate->translate('Sign up for our newsletter!').'</h2>';
       }
       return $signUpHeader;
    }
}
?>