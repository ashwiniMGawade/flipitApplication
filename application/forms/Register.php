<?php
class Application_Form_Register extends Application_Form_Base
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
        $vistorEmail->setLabel('Email');

        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setRequired(true);
        $vistorPassword->setAttrib('class', 'form-control');
        $vistorPassword->setLabel('Password');
        $vistorPassword->setAttrib('autocomplete', 'off');

        $vistorFirstName = new Zend_Form_Element_Text('firstName');
        $vistorFirstName->setRequired(true);
        $vistorFirstName->setAttrib('class', 'form-control');
        $vistorFirstName->setLabel('First name');

        $vistorLastName = new Zend_Form_Element_Text('lastName');
        $vistorLastName->setRequired(true);
        $vistorLastName->setAttrib('class', 'form-control');
        $vistorLastName->setLabel('Last name');

        $vistorDateOfBirth = new Zend_Form_Element_Text('dateOfBirth');
        $vistorDateOfBirth->setAttrib('class', 'form-control');
        $vistorDateOfBirth->setLabel('Date of Birth');

        $vistorDateOfBirthDay = new Zend_Form_Element_Text('dateOfBirthDay');
        $vistorDateOfBirthDay->setRequired(true);
        $vistorDateOfBirthDay->setAttribs(array('class'=>'form-control', 'size'=> 2, 'maxlength' => 2));
        $vistorDateOfBirthDay->addValidator('Digits');

        $vistorDateOfBirthMonth = new Zend_Form_Element_Text('dateOfBirthMonth');
        $vistorDateOfBirthMonth->setRequired(true);
        $vistorDateOfBirthMonth->setAttribs(array('class'=>'form-control', 'size'=> 2, 'maxlength' => 2));
        $vistorDateOfBirthMonth->addValidator('Digits');

        $vistorDateOfBirthYear = new Zend_Form_Element_Text('dateOfBirthYear');
        $vistorDateOfBirthYear->setRequired(true);
        $vistorDateOfBirthYear->setAttrib('class', 'form-control');
        $vistorDateOfBirthYear->setAttribs(array('class'=>'form-control', 'size'=> 4, 'maxlength' => 4));
        $vistorDateOfBirthYear->addValidator('Digits');

        $vistorGender = new Zend_Form_Element_Select('gender');
        $vistorGender->setRequired(true);
        $vistorGender->setAttrib('class', 'form-control');
        $vistorGender->setLabel('Gender');
        $vistorGender->addMultiOptions(array('M'=>'Male', 'F'=>'Female'));

        $vistorPostalCode = new Zend_Form_Element_Text('postCode');
        $vistorPostalCode->setRequired(true);
        $vistorPostalCode->setAttrib('class', 'form-control');
        $vistorPostalCode->setLabel('Postcode');
        $this->addElements(
            array(
                $vistorEmail,
                $vistorPassword,
                $vistorFirstName,
                $vistorLastName,
                $vistorDateOfBirth,
                $vistorDateOfBirthDay,
                $vistorDateOfBirthMonth,
                $vistorDateOfBirthYear,
                $vistorGender,
                $vistorPostalCode
            )
        );
        
    }
}
