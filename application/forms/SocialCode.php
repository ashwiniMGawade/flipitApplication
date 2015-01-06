<?php
class Application_Form_SocialCode extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $nickname = new Zend_Form_Element_Text('nickname');
        $nickname->setRequired(true);
        $nickname->setAttrib('class', 'form-control');
        $nickname->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_nickname'));

        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true);
        $title->setAttrib('class', 'form-control');
        $title->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Offer Title'));

        $offerUrl = new Zend_Form_Element_Text('offerUrl');
        $offerUrl->setRequired(true);
        $offerUrl->setAttrib('class', 'form-control');
        $offerUrl->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Offer URL'));

        $code = new Zend_Form_Element_Text('code');
        $code->setRequired(true);
        $code->setAttrib('class', 'form-control');
        $code->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon code'));

        $expireDate = new Zend_Form_Element_Text('expireDate');
        $expireDate->setRequired(true);
        $expireDate->setAttrib('class', 'form-control');
        $expireDate->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_expireddate'));

        $offerDetails = new Zend_Form_Element_Textarea('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Offer Details'));

        $shopIdHiddenField =  new Zend_Form_Element_Hidden('shopId');
        $shopPermalinkHiddenField =  new Zend_Form_Element_Hidden('shopPermalink');

        $this->addElements(array(
            $nickname,
            $title,
            $offerUrl,
            $code,
            $expireDate,
            $offerDetails,
            $shopIdHiddenField,
            $shopPermalinkHiddenField
        ));
    }
}
