<?php
class Application_Form_SocialCode extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $shops = new Zend_Form_Element_Text('shops');
        $shops->setRequired(true);
        $shops->setAttrib('class', 'form-control');
        $shops->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_shops'));
        $shops->addFilter('StringTrim');
        $shops->addFilter('StripTags');
        $shops->setAttrib('id', 'searchShops');
       
        $code = new Zend_Form_Element_Text('code');
        $code->setRequired(true);
        $code->setAttrib('class', 'form-control');
        $code->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon code'));
        $code->addFilter('StringTrim');
        $code->addFilter('StripTags');
        
        $offerDetails = new Zend_Form_Element_Text('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Offer Details'));
        $offerDetails->addFilter('StringTrim');
        $offerDetails->addFilter('StripTags');
        
        $expireDate = new Zend_Form_Element_Text('expireDate');
        $expireDate->setAttrib('class', 'form-control');
        $expireDate->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_expirydate'));
        $expireDate->addValidator('regex', false, array('/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(|-|)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(|-|)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(|-|)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/'));
        $expireDate->addValidator(new Application_Form_DateGreaterThanToday());
        $expireDate->addFilter('StringTrim');
        $expireDate->addFilter('StripTags');
        $this->addElements(array(
            $shops,
            $code,
            $offerDetails,
            $expireDate
        ));
    }
}
