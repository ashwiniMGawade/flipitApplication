<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\Settings;

class SettingsValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Settings $settings)
    {
        $constraints = array();
        $constraints['updated_at'][] = $this->validator->dateTime();
        return $this->validator->validate($settings, $constraints);
    }
}
