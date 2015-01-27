<?php
class Application_Form_SocialCodeSettingForm extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $nickname = new Zend_Form_Element_Text('nickname');
        $validator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
        $nickname->setRequired(true);
        $nickname->setAttrib('class', 'form-control');
        $nickname->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_nickname'));
        $nickname->addValidator($validator);

        $store = new Zend_Form_Element_Select('store');
        $store->setRequired(true);
        $store->setAttrib('class', 'form-control');
        $shopsValues = '';
        $shop = new Shop();
        $allShops = $shop->getAllShopNames();
        foreach ($allShops as $shop) {
            $shopsValues[$shop['id']] = $shop['name'];
        }
        $store->addMultiOptions($shopsValues);
        
        $expireDate = new Zend_Form_Element_Text('expireDate');
        $expireDate->setRequired(true);
        $expireDate->setAttrib('class', 'form-control');
        $expireDate->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_expireddate'));
        $expireDate->addValidator('date', true, array('dd-mm-yyyy'));

        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true);
        $title->setAttrib('class', 'form-control');
        $title->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon title'));

        $offerUrl = new Zend_Form_Element_Text('offerUrl');
        $offerUrl->setRequired(true);
        $offerUrl->setAttrib('class', 'form-control');
        $offerUrl->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon link'));

        $code = new Zend_Form_Element_Text('code');
        $code->setRequired(true);
        $code->setAttrib('class', 'form-control');
        $code->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Coupon code'));

        $offerDetails = new Zend_Form_Element_Textarea('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Term and Conditions'));

        $this->addElements(array(
            $nickname,
            $store,
            $expireDate,
            $title,
            $offerUrl,
            $code,
            $offerDetails
        ));
    }
}
