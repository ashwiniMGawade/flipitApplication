<?php
class Application_Form_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $format = '<input class="%s" placeholder="%s"  name="%s"  id="%s" type="%s" value="%s"/>';

    public function render($content)
    {
        $element = $this->getElement();
        $elementName = htmlentities($element->getFullyQualifiedName());
        $elementId = htmlentities($element->getId());
        $elementValue = htmlentities($element->getValue());
        $elementClass = htmlentities($element->getAttrib('class'));
        $elementPlaceHolder = htmlentities($element->getAttrib('placeholder'));
        $elementType = htmlentities($element->getAttrib('type'));
        $markup = sprintf($this->format, $elementClass, $elementPlaceHolder, $elementName, $elementId, $elementType, $elementValue);
        return $markup;
    }
}
