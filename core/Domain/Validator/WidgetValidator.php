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
        $constraints = $this->setDefaultValidationRules();
        if (!is_null($widget->getStartDate())) {
            $constraints['endDate'][] = $this->validator->notNull(array('message'=>'End date should not be blank.'));

            if (!is_null($widget->getEndDate())) {
                $constraints['endDate'][] = $this->validator->greaterThan(array('value' => $widget->getStartDate(), 'message' => 'End date should be greater than start date.'));
            }
        }
        if (!is_null($widget->getEndDate())) {
            $constraints['startDate'][] = $this->validator->notNull(array('message'=>'Start date should not be blank.'));
            $constraints['endDate'][] = $this->validator->greaterThan(array('value' => 'yesterday', 'message' => 'End date must be future date.'));
        }
        return $this->validator->validate($widget, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'title' => array(
                $this->validator->notNull(array('message'=>'Title should not be blank.'))
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
