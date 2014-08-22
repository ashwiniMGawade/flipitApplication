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
        $searchBrand->setRequired(true);
        $searchBrand->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_Type in a Store e.g.  Avis'));
        $searchBrand->setAttrib('class', 'form-control');
        $this->addElements(array($searchBrand));
    }
}
