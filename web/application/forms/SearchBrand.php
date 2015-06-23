<?php
class Application_Form_SearchBrand extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $searchBrand = new Zend_Form_Element_Text('searchBrand');
        $searchBrand->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Search brand'));
        $searchBrand->setAttrib('class', 'form-control text-active');
        $searchBrand->setAttrib('id', 'searchFieldBrandHeader');
        $searchBrandHidden = new Zend_Form_Element_Hidden('searchedBrandKeyword');
        $this->addElements(array($searchBrand, $searchBrandHidden));
    }
}