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
        $decoratorEmailAddress = new Application_Form_SimpleInput();
        $emailAddressTextBox = new Zend_Form_Element_Text(
            'emailAddress',
            array(
                'decorators' => array(
                   $decoratorEmailAddress,'Errors',
                )
            )
        );
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

        $hiddenFieldForShopId = new Zend_Form_Element_Hidden('shopId');
        $hiddenFieldForShopId->setDecorators(array('ViewHelper'));

        $submitButton = new Zend_Form_Element_Submit('Sign Up');
        $submitButton->setAttrib('type', 'submit');
        $submitButton->setAttrib('id', 'Login');
        $submitButton->setLabel($this->submitButtonLabel);
        $submitButton->setAttrib('class', 'btn blue btn-lg btn-primary '.$this->submitButtonClassName);
        $submitButton->setAttrib('onclick', 'setHiddenFieldValue(), signUpNewsLetter("' . $this->zendFormName .'")');
        $submitButton->setDecorators(array('ViewHelper'));

        $this->addElement($emailAddressTextBox)
            ->addElement($submitButton)
            ->addElement($hiddenFieldForShopId)
            ->setAttrib('id', $this->zendFormName)
            ->setAttrib('name', $this->zendFormName)
            ->setAttrib('class', 'form-signin newsletter '. $this->zendFormClassName)
            ->setMethod('POST')
            ->setDecorators(array('FormElements','Form'));
    }
}
