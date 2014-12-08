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
        $nickname->setAttrib('placeholder', 'nickname');

        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true);
        $title->setAttrib('class', 'form-control');
        $title->setAttrib('placeholder', 'Offer Title');

        $offerUrl = new Zend_Form_Element_Text('offerUrl');
        $offerUrl->setRequired(true);
        $offerUrl->setAttrib('class', 'form-control');
        $offerUrl->setAttrib('placeholder', 'Offer URL');

        $code = new Zend_Form_Element_Text('code');
        $code->setRequired(true);
        $code->setAttrib('class', 'form-control');
        $code->setAttrib('placeholder', 'Coupon code');

        $expireDate = new Zend_Form_Element_Text('expireDate');
        $expireDate->setRequired(true);
        $expireDate->setAttrib('class', 'form-control');
        $expireDate->setAttrib('placeholder', 'Expire Date');

        $offerDetails = new Zend_Form_Element_Textarea('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', 'Offer Details');

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
