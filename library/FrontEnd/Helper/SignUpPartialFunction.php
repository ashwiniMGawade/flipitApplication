<?php
class FrontEnd_Helper_SignUpPartialFunction extends Transl8_View_Helper_Translate
{
    public static function validateZendForm($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget)
    {
        self::checkFormIsValidOrNot($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget);
    }
    public static function createFormForSignUp($formName, $submitButtonLabel, $zendFormClassName = '', $submitButtonClassName = '')
    {
        return new Application_Form_SignUp($formName, $submitButtonLabel, $zendFormClassName, $submitButtonClassName);
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
        $visitorEmail = new Zend_Session_Namespace('emailAddressSignup');
        $visitorEmail->emailAddressSignup = $emailAddress;
        $addToFavoriteShopId = $signUpNewsLetterform->getValue('shopId');
        $visitorShopId = new Zend_Session_Namespace('shopId');
        $visitorShopId->shopId = $addToFavoriteShopId;
        $signUpStep2Url= HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven');
        return $signUpStep2Url;
    }

    public function getSignUpWidgetHeader($widgetType)
    {
       if ($widgetType== 'sidebarWidget') {
        $signUpHeader='<h2 class="form-signin-heading">' .$this->translate('Sign up').'
            <span>'.$this->translate('and join over').'<br>' .$this->translate('10 million people')
            .'</span></h2>';
       } else if($widgetType == 'categoryPageSignupForm') {
        $signUpHeader='<h2 class="form-signin-heading">' .'
            <span>'.$this->translate('Sign up for our newsletter and receive discounts inside your mailbox').'!'
            .'</span></h2>';
       }
       else if($widgetType == 'footerSignupForm') {
        $signUpHeader='<div class="text">
                <h2>'.$this->translate('Subscribe now').'</h2>
                <span>'.$this->translate('Become a saving superstar').'! '.'<br>'.$this->translate('And get exclusive codes').'</span>
            </div>';
       }
       else {
         $signUpHeader='<h2>'.$this->translate('Receive weekly updates of the best offers?').'<br>'
         .$this->translate('Sign up for our newsletter!').'</h2>';
       }
       return $signUpHeader;
    }
}
?>