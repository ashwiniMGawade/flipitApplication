<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\LocaleSettings;

class LocaleSettingValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(LocaleSettings $localeSetting)
    {
        $constraints = array(
            'locale' => array(
                $this->validator->notNull(array('message'=>'locale should not be blank.'))
            ),
            'timezone' => array(
                $this->validator->notNull(array('message'=>'timezone should not be blank.'))
            )
        );
        return $this->validator->validate($localeSetting, $constraints);
    }
}
