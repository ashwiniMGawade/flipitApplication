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
        $nickname->setRequired(true);
        $nickname->setAttrib('class', 'form-control');
        $nickname->setAttrib('placeholder', 'nickname');

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
        
        $day = new Zend_Form_Element_Select('day');
        $day->setRequired(true);
        $day->setAttrib('class', 'form-control');
        $dayValue = '';
        for ($start = 1; $start <=31; $start++) {
            if ($start < 10) {
                $start = 0 . $start;
            }
            $dayValue[$start] = $start;
        }
        $day->addMultiOptions($dayValue);

        $month = new Zend_Form_Element_Select('month');
        $month->setRequired(true);
        $month->setAttrib('class', 'form-control');
        $monthValue = '';
        for ($start = 1; $start <=12; $start++) {
            if ($start < 10) {
                $start = 0 . $start;
            }
            $monthValue[$start] = $start;
        }
        $month->addMultiOptions($monthValue);

        $year = new Zend_Form_Element_Select('year');
        $year->setRequired(true);
        $year->setAttrib('class', 'form-control');
        $yearValue = '';
        for ($start = 2000; $start <=2050; $start++) {
            $yearValue[$start] = $start;
        }
        $year->addMultiOptions($yearValue);

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

        $offerDetails = new Zend_Form_Element_Textarea('offerDetails');
        $offerDetails->setRequired(true);
        $offerDetails->setAttrib('class', 'form-control');
        $offerDetails->setAttrib('placeholder', 'Offer Details');

        $this->addElements(array(
            $nickname,
            $store,
            $day,
            $month,
            $year,
            $title,
            $offerUrl,
            $code,
            $offerDetails
        ));
    }
}
