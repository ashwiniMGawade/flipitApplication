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
        switch ($settings->getName()) {
            case 'HTML_LANG':
                $constraints['value'][] = $this->validator->type(array('type' => 'alpha', 'message' => 'HTML lang must contain only alphabets'));
                break;
            default:
                break;
        }
        return $this->validator->validate($settings, $constraints);
    }
}
