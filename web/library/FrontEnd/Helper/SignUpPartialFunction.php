<?php
class FrontEnd_Helper_SignUpPartialFunction
{
    public static function validateZendForm($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget)
    {
        self::checkFormIsValidOrNot($currentSubmittedForm, $singUpFormForStorePage, $signUpFormSidebarWidget);
    }
    public static function createFormForSignUp(
    	$formName,
    	$submitButtonLabel,
    	$zendFormClassName = '',
    	$submitButtonClassName = ''
    ) {
        return new Application_Form_SignUp($formName, $submitButtonLabel, $zendFormClassName, $submitButtonClassName);
    }

    public static function checkFormIsValidOrNot(
    	$currentSubmittedForm,
    	$signUpFormForStorePage,
    	$signUpFormSidebarWidget
    ) {
        if ($currentSubmittedForm->getRequest()->isPost()) {
            $whichFormIsPostForValidation  ='';
            switch ($currentSubmittedForm->getRequest()->getParam('formName')) {
                case 'largeSignupForm':
                    $whichFormIsPostForValidation = $signUpFormForStorePage;
                    break;
                case 'formSignupSidebarWidget':
                    $whichFormIsPostForValidation = $signUpFormSidebarWidget;
                    break;
                default:
                    $whichFormIsPostForValidation = $signUpFormForStorePage;
                    break;
            }
            if (is_object($whichFormIsPostForValidation)) {
                if ($whichFormIsPostForValidation->isValid($currentSubmittedForm->getRequest()->getPost())) {
                    $signUpStep2Url = self::signUpRedirectLink($whichFormIsPostForValidation);
                    header('location:' . $signUpStep2Url);
                    exit();
                } else {
                    $whichFormIsPostForValidation->highlightErrorElements();
                }
            }
        }
        return true;
    }

    public static function signUpRedirectLink($signUpNewsLetterform)
    {
        $emailAddress = $signUpNewsLetterform->getValue('emailAddress');
        $visitorEmail = new Zend_Session_Namespace('emailAddressSignup');
        $visitorEmail->emailAddressSignup = $emailAddress;
        $addToFavoriteShopId = $signUpNewsLetterform->getValue('shopId');
        $visitorShopId = new Zend_Session_Namespace('shopId');
        $visitorShopId->shopId = $addToFavoriteShopId;
        $signUpStep2Url= HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven');
        return $signUpStep2Url;
    }

    public function getSignUpWidgetHeader($widgetType)
    {
       if ($widgetType== 'sidebarWidget') {
        $signUpHeader=
        	'<h4 class="form-signin-heading">' .FrontEnd_Helper_viewHelper::__translate('Sign up').'
            <span>'.FrontEnd_Helper_viewHelper::__translate('and join over').'<br>' 
            .FrontEnd_Helper_viewHelper::__translate('10 million people')
            .'</span></h4>';
       } else if($widgetType == 'categoryPageSignupForm') {
        $signUpHeader='<h2 class="form-signin-heading">' .'
            <span>'.FrontEnd_Helper_viewHelper::__translate('Sign up for our newsletter and receive discounts inside your mailbox').'!'
            .'</span></h2>';
       }
       else if($widgetType == 'footerSignupForm') {
        $signUpHeader='<div class="text">
                <h4>'.FrontEnd_Helper_viewHelper::__translate('Subscribe now').'</h4>
                <span>'.FrontEnd_Helper_viewHelper::__translate('Become a saving superstar&#33;').'<br>'
                .FrontEnd_Helper_viewHelper::__translate('And get exclusive codes').'</span>
            </div>';
       }
       else {
         $signUpHeader='<h4>'.FrontEnd_Helper_viewHelper::__translate('Receive weekly updates of the best offers?')
         .'<br>'
         .FrontEnd_Helper_viewHelper::__translate('Sign up for our newsletter!').'</h4>';
       }
       return $signUpHeader;
    }
}
?>