<?php

namespace Core\Service;

use \Core\Service\Errors\ErrorsInterface;

class Errors implements ErrorsInterface
{
    protected $errors;
    protected $originalState;

    public function setError($errorMessage, $fieldName = '')
    {
        if (!empty($fieldName) && !empty($errorMessage)) {
            $this->errors[$fieldName] = $errorMessage;
        } elseif (!empty($errorMessage)) {
            $this->errors[] = $errorMessage;
        }
    }

    public function setErrors($errorMessages = array())
    {
        foreach ($errorMessages as $fieldName => $fieldMessages) {
            $this->errors[$fieldName] = $fieldMessages;
        }
    }

    public function setOriginalState($originalState)
    {
        if (!empty($originalState)) {
            $this->originalState = $originalState;
        }
    }

    public function getError($fieldName)
    {
        if (array_key_exists($fieldName, $this->errors)) {
            return $this->errors[$fieldName];
        }
    }

    public function getErrorsAll()
    {
        return $this->errors;
    }

    public function getOriginalState()
    {
        return $this->originalState;
    }

    public function getErrorMessages()
    {
        $errorMessages = array();
        foreach ($this->errors as $fieldName => $fieldMessages) {
            if (is_array($fieldMessages)) {
                $errorMessages[] = $fieldName . ": " . implode(', ', $fieldMessages);
            } else {
                $errorMessages[] = $fieldName . ": " . $fieldMessages;
            }
        }
        return $errorMessages;
    }
}
