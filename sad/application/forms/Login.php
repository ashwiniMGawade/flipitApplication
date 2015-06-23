<?php
class Application_Form_Login extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $vistorEmail = new Zend_Form_Element_Text('emailAddress');
        $vistorEmail->setRequired(true);
        $vistorEmail->addValidator(
            'EmailAddress',
            true,
            array(
                'messages' => array(Zend_Validate_EmailAddress::INVALID_FORMAT=>'Please enter valid email address')
            )
        );
        $vistorEmail->setAttrib('class', 'form-control');
        $vistorEmail->setAttrib('tabindex', '1');
        $vistorEmail->setAttrib('placeholder', 'E-mail');
        $vistorEmail->setLabel(FrontEnd_Helper_viewHelper::__form('form_Email address'));

        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setRequired(true);
        $vistorPassword->setAttrib('class', 'form-control');
        $vistorPassword->setAttrib('tabindex', '2');
        $vistorPassword->setLabel(FrontEnd_Helper_viewHelper::__form('form_Password'));
        $vistorPassword->setAttrib('autocomplete', 'off');
        $this->addElements(array($vistorEmail, $vistorPassword));
    }
}
