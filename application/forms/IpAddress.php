<?php
class Application_Form_IpAddress extends Application_Form_AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $validator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
        $name->setRequired(true);
        $name->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_name'));
        $name->addValidator($validator);

        $ipaddress = new Zend_Form_Element_Text('ipaddress');
        $ipaddress->setRequired(true);
        $validator = new Zend_Validate_Ip();
        $ipaddress->addValidator($validator);
        $ipaddress->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_ipaddress'));
        $ipaddress->addValidator($validator);

        $editId =  new Zend_Form_Element_Hidden('id');
        $qString =  new Zend_Form_Element_Hidden('qString');
        $this->addElements(array($name, $ipaddress, $editId, $qString));
    }
}
