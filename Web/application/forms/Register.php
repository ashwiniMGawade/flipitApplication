<?php
class Application_Form_Register extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
         $visitorEmail = new Zend_Form_Element_Text('emailAddress');
         $visitorEmail->setRequired(true);
         $visitorEmail->addValidator(
             'EmailAddress',
             true,
             array(
                 'messages' => array(Zend_Validate_EmailAddress::INVALID_FORMAT=>'Please enter valid email address')
             )
         );
         $visitorEmail->setAttrib('class', 'form-control');
         $visitorEmail->setAttrib('placeholder', 'E-mail');
         $visitorEmail->setLabel(FrontEnd_Helper_viewHelper::__form('form_Email'));

        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setRequired(true);
        $vistorPassword->setAttribs(
            array('class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorPassword->setLabel(FrontEnd_Helper_viewHelper::__form('form_Password'));
        $vistorPassword->setAttrib('autocomplete', 'off');

        $vistorFirstName = new Zend_Form_Element_Text('firstName');
        $vistorFirstName->setRequired(true);
        $vistorFirstName->setAttrib('class', 'form-control');
        $vistorFirstName->setLabel(FrontEnd_Helper_viewHelper::__form('form_First name'));

        $vistorLastName = new Zend_Form_Element_Text('lastName');
        $vistorLastName->setRequired(true);
        $vistorLastName->setAttrib('class', 'form-control');
        $vistorLastName->setLabel(FrontEnd_Helper_viewHelper::__form('form_Last name'));
        
        $vistorGender = new Zend_Form_Element_Select('gender');
        $vistorGender->setRequired(true);
        $vistorGender->setAttrib('class', 'form-control');
        $vistorGender->setLabel(FrontEnd_Helper_viewHelper::__form('form_Gender'));
        $vistorGender->addMultiOptions(array('M'=>'Male', 'F'=>'Female'));

        $shopIdHiddenField =  new Zend_Form_Element_Hidden('shopId');
        $this->addElements(
            array(
                $visitorEmail,
                $vistorPassword,
                $vistorFirstName,
                $vistorLastName,
                $vistorGender,
                $shopIdHiddenField
            )
        );

    }
}
