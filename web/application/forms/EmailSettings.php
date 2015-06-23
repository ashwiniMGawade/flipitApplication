<?php
class Application_Form_EmailSettings extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $senderEmail = new Zend_Form_Element_Text('senderEmail');
        $senderEmail->setRequired(true);
        $senderEmail->addValidator(
            'EmailAddress',
            true,
            array(
                'messages' => array(Zend_Validate_EmailAddress::INVALID_FORMAT=>'Please enter valid email address')
            )
        );
        $senderEmail->setAttrib('class', 'span3');
        $senderEmail->setLabel(FrontEnd_Helper_viewHelper::__form('form_Sender Email Address'));
        $senderName = new Zend_Form_Element_Text('senderName');
        $senderName->setRequired(true);
        $senderName->setAttrib('class', 'span3');
        $senderName->setLabel(FrontEnd_Helper_viewHelper::__form('form_Sender Name'));
        $this->addElements(array($vistorEmail, $senderName));
    }
}
