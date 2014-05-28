<?php
class Application_Form_Profile extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $visitorEmail = new Zend_Form_Element_Text('emailAddress');
        $visitorEmail->setAttribs(array('class' => 'form-control'));
        $visitorEmail->setLabel('Email');

        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setRequired(true);
        $vistorPassword->setAttribs(
            array('autocomplete', 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorPassword->setLabel('Password');

        $vistorConfirmPassword = new Zend_Form_Element_Password('confirmPassword');
        $vistorConfirmPassword->setRequired(true);
        $vistorConfirmPassword->setAttribs(
            array('autocomplete', 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorConfirmPassword->setLabel('Confirm Password');
        $vistorConfirmPassword->addValidator('Identical', false, array('token' => 'password'));

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

        $vistorNewsLetterStatus = new Zend_Form_Element_Checkbox('weeklyNewsLetter');

        $this->addElements(
            array(
                $visitorEmail,
                $vistorPassword,
                $vistorConfirmPassword,
                $vistorFirstName,
                $vistorLastName,
                $vistorDateOfBirth,
                $vistorDateOfBirthDay,
                $vistorDateOfBirthMonth,
                $vistorDateOfBirthYear,
                $vistorGender,
                $vistorPostalCode,
                $vistorNewsLetterStatus
            )
        );
        
    }
}
