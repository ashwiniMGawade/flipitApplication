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
        $visitorEmail->setLabel(FrontEnd_Helper_viewHelper::__form('form_Email'));
        $visitorEmail->setAttribs(
            array('readonly' => 'readonly', 'class'=>'form-control', 'disabled'=> 'disabled')
        );

        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setAttribs(
            array('autocomplete'=> 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorPassword->setLabel(FrontEnd_Helper_viewHelper::__form('form_Password'));

        $vistorConfirmPassword = new Zend_Form_Element_Password('confirmPassword');
        $vistorConfirmPassword->setAttribs(
            array('autocomplete'=> 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorConfirmPassword->setLabel(FrontEnd_Helper_viewHelper::__form('form_Confirm Password'));
        $vistorConfirmPassword->addValidator('Identical', false, array('token' => 'password'));

        $vistorFirstName = new Zend_Form_Element_Text('firstName');
        $vistorFirstName->setRequired(true);
        $vistorFirstName->setAttrib('class', 'form-control');
        $vistorFirstName->setLabel(FrontEnd_Helper_viewHelper::__form('form_First name'));

        $vistorLastName = new Zend_Form_Element_Text('lastName');
        $vistorLastName->setRequired(true);
        $vistorLastName->setAttrib('class', 'form-control');
        $vistorLastName->setLabel(FrontEnd_Helper_viewHelper::__form('form_Last name'));

        $vistorDateOfBirth = new Zend_Form_Element_Text('dateOfBirth');
        $vistorDateOfBirth->setAttrib('class', 'form-control');
        $vistorDateOfBirth->setLabel(FrontEnd_Helper_viewHelper::__form('form_Date of Birth'));

        $dayMultiOptions = array('' => '');
        for ($i = 1; $i < 32; $i++) {
            $dayMultiOptions[$i] = $i;
        }
        $vistorDateOfBirthDay = new Zend_Form_Element_Select('dateOfBirthDay');
        $vistorDateOfBirthDay->setRequired(true);
        $vistorDateOfBirthDay->setAttribs(array('class'=>'form-control'));
        $vistorDateOfBirthDay->addMultiOptions($dayMultiOptions);

        $monthMultiOptions = array('' => '');
        for ($i = 1; $i < 13; $i++) {
            $monthMultiOptions[$i] = $i;
        }
        $vistorDateOfBirthMonth = new Zend_Form_Element_Select('dateOfBirthMonth');
        $vistorDateOfBirthMonth->setRequired(true);
        $vistorDateOfBirthMonth->setAttribs(array('class'=>'form-control'));
        $vistorDateOfBirthMonth->addMultiOptions($monthMultiOptions);

        $yearMultiOptions = array('' => '');
        for ($i = 1900; $i <=  date('Y'); $i++) {
            $yearMultiOptions[$i] = $i;
        }

        $vistorDateOfBirthYear = new Zend_Form_Element_Select('dateOfBirthYear');
        $vistorDateOfBirthYear->setRequired(true);
        $vistorDateOfBirthYear->setAttribs(array('class'=>'form-control'));
        $vistorDateOfBirthYear->addMultiOptions($yearMultiOptions);

        $vistorGender = new Zend_Form_Element_Select('gender');
        $vistorGender->setRequired(true);
        $vistorGender->setAttrib('class', 'form-control');
        $vistorGender->setLabel(FrontEnd_Helper_viewHelper::__form('form_Gender'));
        $vistorGender->addMultiOptions(array('M'=>'Male', 'F'=>'Female'));

        $vistorPostalCode = new Zend_Form_Element_Text('postCode');
        $vistorPostalCode->setRequired(true);
        $vistorPostalCode->setAttrib('class', 'form-control');
        $vistorPostalCode->setLabel(FrontEnd_Helper_viewHelper::__form('form_Postcode'));

        $vistorNewsLetterStatus = new Zend_Form_Element_Checkbox('weeklyNewsLetter');
        $codeAlertStatus = new Zend_Form_Element_Checkbox('codealert');

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
                $vistorNewsLetterStatus,
                $codeAlertStatus
            )
        );

    }
}
