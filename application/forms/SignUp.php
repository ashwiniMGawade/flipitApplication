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
        $emailAddressTextBox->setAttrib('placeholder', 'Email address');
        $emailAddressTextBox->setAttrib('type', 'email');
        $emailAddressTextBox->setLabel('Email address');
        
        $shopIdHiddenField =  new Zend_Form_Element_Hidden('shopId');
        $this->addElements(array($emailAddressTextBox, $shopIdHiddenField));
    }
}
