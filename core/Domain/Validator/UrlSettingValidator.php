<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\URLSetting;

class UrlSettingValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(URLSetting $urlSetting)
    {
        $constraints = array(
            'url' => array(
                $this->validator->notNull(array('message'=>'URL should not be blank.'))
            ),
            'status' => array(
                $this->validator->notNull(array('message'=>'Status should not be blank.')),
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'createdAt' => array(
                $this->validator->dateTime()
            ),
            'updatedAt' => array(
                $this->validator->dateTime()
            )
        );
        return $this->validator->validate($urlSetting, $constraints);
    }
}
