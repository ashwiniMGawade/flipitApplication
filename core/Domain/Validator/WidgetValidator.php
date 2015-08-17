<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\Widget;

class WidgetValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Widget $widget)
    {
        $constraint = $this->setDefaultValidationRules();
        return $this->validator->validate($widget, $constraint);
    }

    private function setDefaultValidationRules()
    {
        $rules = array(
            'title' => array(
                $this->validator->notBlank()
            )
        );
        return $rules;
    }
}
