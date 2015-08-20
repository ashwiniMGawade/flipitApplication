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
        $constraints = $this->setDefaultValidationRules($widget);
        if (!is_null($widget->getStartDate())) {
            $constraints['endDate'][] = $this->validator->notNull();
        }
        if (!is_null($widget->getEndDate())) {
            $constraints['startDate'][] = $this->validator->notNull();
            $constraints['endDate'][] = $this->validator->greaterThan($widget->getStartDate());
        }
        return $this->validator->validate($widget, $constraints);
    }

    private function setDefaultValidationRules($widget)
    {
        $constraints = array(
            'title' => array(
                $this->validator->notBlank()
            ),
            'deleted' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'created_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            ),
            'updated_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            ),
            'startDate' => array(
                $this->validator->dateTime()
            ),
            'endDate' => array(
                $this->validator->dateTime()
            )
        );
        return $constraints;
    }
}
