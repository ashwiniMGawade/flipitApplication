<?php
class Application_Form_ResetPassword extends Application_Form_Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        $vistorPassword = new Zend_Form_Element_Password('password');
        $vistorPassword->setRequired(true);
        $vistorPassword->setAttribs(
            array('autocomplete'=> 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorPassword->setLabel('Password');

        $vistorConfirmPassword = new Zend_Form_Element_Password('confirmPassword');
        $vistorConfirmPassword->setRequired(true);
        $vistorConfirmPassword->setAttribs(
            array('autocomplete'=> 'off', 'class'=>'form-control', 'minlength'=> 1, 'maxlength' => 20)
        );
        $vistorConfirmPassword->setLabel('Confirm Password');
        $vistorConfirmPassword->addValidator('Identical', false, array('token' => 'password'));
        $this->addElements(
            array(
                $vistorPassword,
                $vistorConfirmPassword,
            )
        );

    }
}
