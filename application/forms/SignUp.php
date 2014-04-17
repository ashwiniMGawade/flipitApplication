<?php
class Application_Form_SignUp extends Application_Form_Base
{
    public function init()
    {
        $decoratorUsername = new Application_Form_SimpleInput();
        $emailAddressTextBox = new Zend_Form_Element_Text(
            'emailAddress',
            array(
                'decorators' => array(
                   $decoratorUsername,'Errors',
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

        $submitButton = new Zend_Form_Element_Button('sign up');
        $submitButton->setAttrib('type', 'submit');
        $submitButton->setAttrib('id', 'Login');
        $submitButton->setAttrib('class', 'btn blue btn-lg btn-primary');
        $submitButton->setAttrib('onclick', 'setHiddenFieldValue(), signUpNewsLetter()');
        $submitButton->setDecorators(array('ViewHelper'));

        $this->addElement($emailAddressTextBox)
            ->addElement($submitButton)
            ->addElement($hiddenFieldForShopId)
            ->setAttrib('id', 'login')
            ->setAttrib('id', 'formOneHomePage')
            ->setAttrib('class', 'form-signin newsletter')
            ->setMethod('POST')
            ->setDecorators(array('FormElements','Form'));
    }
}
