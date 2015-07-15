<?php
class Application_Form_Base extends Zend_Form
{
    public function __construct()
    {
        // this is where i normally set up my decorators for the form and elements
        // additionally you can register prefix paths for custom validators, decorators, and elements

        parent::__construct();
        // parent::__construct must be called last because it calls $form->init()
        // and anything after it is not executed
    }

    public function highlightErrorElements()
    {
        foreach ($this->getElements() as $element) {
            $elementValue = $element->getValue();
            if ($element->getType()!= 'Zend_Form_Element_Checkbox') {
                if ($element->hasErrors()) {
                    $element->setAttrib('class', 'input-error form-control');
                } elseif ($element->isValid($elementValue)) {
                    $element->setAttrib('class', 'input-success form-control');
                }
            } else if ($element->getType() == 'Zend_Form_Element_Checkbox') {
                if ($element->hasErrors()) {
                    $element->setAttrib('class', 'input-error');
                } elseif ($element->isValid($elementValue)) {
                    $element->setAttrib('class', 'input-success');
                }
            }
        }
    }
}
