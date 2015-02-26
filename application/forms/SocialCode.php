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
       
        $code = new Zend_Form_Element_Text('code');
        $code->setRequired(true);
        $code->setAttrib('class', 'form-control');
        $code->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon code'));
        
        $offerDetails = new Zend_Form_Element_Text('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Offer Details'));

        $expireDate = new Zend_Form_Element_Text('expireDate');
        $expireDate->setAttrib('class', 'form-control');
        $expireDate->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_expireddate'));
        //$validator = new Zend_Validate_Date(array('format' => 'd-m-Y'));
        //$expireDate->addValidator($validator);
        $expireDate->addValidator(new Application_Form_DateGreaterThanToday());

        $shopIdHiddenField =  new Zend_Form_Element_Hidden('shopId');

        $this->addElements(array(
            $shops,
            $code,
            $offerDetails,
            $expireDate,
            $shopIdHiddenField
        ));
    }
}
