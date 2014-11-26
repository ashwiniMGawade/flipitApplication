<?php
class Application_Form_SignUp extends Application_Form_Base
{
    public $zendFormName = '';
    public $submitButtonLabel = '';
    public $zendFormClassName = '';
    public $submitButtonClassName = '';
    public function __construct($zendFormName, $submitButtonLabel, $zendFormClassName = '', $submitButtonClassName = '')
    {
        $this->zendFormName = $zendFormName;
        $this->submitButtonLabel = $submitButtonLabel;
        $this->zendFormClassName = $zendFormClassName;
        $this->submitButtonClassName = $submitButtonClassName;
        parent::__construct();
    }

    public function init()
    {
        $emailAddressTextBox = new Zend_Form_Element_Text('emailAddress');
        $emailAddressTextBox->setRequired(true)->addErrorMessage('');
        $emailAddressTextBox->addValidator(
            'EmailAddress',
            true,
            array(
               'messages' => array(
               Zend_Validate_EmailAddress::INVALID_FORMAT=>
               'Please enter valid email address',
            )
         )
        );
        $emailAddressTextBox->setAttrib('class', 'form-control');
        $emailAddressTextBox->setAttrib('placeholder', FrontEnd_Helper_viewHelper::__form('form_email_address'));
        $emailAddressTextBox->setAttrib('type', 'email');
        $emailAddressTextBox->setLabel(FrontEnd_Helper_viewHelper::__form('from_email_address'));
        
        $shopIdHiddenField =  new Zend_Form_Element_Hidden('shopId');
        $membersonlyHiddenField =  new Zend_Form_Element_Hidden('membersOnly');
        $formNameIdHiddenField =  new Zend_Form_Element_Hidden('formName');
        $formNameIdHiddenField->setValue($this->zendFormName);
        $this->addElements(array($emailAddressTextBox, $shopIdHiddenField,  $formNameIdHiddenField, $membersonlyHiddenField));
    }
}
