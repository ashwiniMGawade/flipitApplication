<?php
class Application_Form_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $format = '<input class="%s" placeholder="%s"  name="%s"  id="%s" type="%s" value="%s"/>';

    public function render($content)
    {
        $element = $this->getElement();
        $name = htmlentities($element->getFullyQualifiedName());
        $id = htmlentities($element->getId());
        $value = htmlentities($element->getValue());
        $class = htmlentities($element->getAttrib('class'));
        $placeHolder = htmlentities($element->getAttrib('placeholder'));
        $type = htmlentities($element->getAttrib('type'));
        $markup = sprintf($this->format, $class, $placeHolder, $name, $id, $type, $value);
        return $markup;
    }
}
