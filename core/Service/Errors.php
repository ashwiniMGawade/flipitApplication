<?php

namespace Core\Service;

class Errors
{
    protected $errors;

    public function setError($fieldName, $errorMessage)
    {
        if ( strlen($fieldName) > 0 && strlen($errorMessage) > 0 ) {
            $this->errors[$fieldName] = $errorMessage;
        }
    }

    public function setErrors($errorMessages)
    {
        foreach ($errorMessages as $fieldName => $fieldMessages) {
            $this->errors[$fieldName] = $fieldMessages;
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

    public function getErrorMessages()
    {
        $errorMessages = array();
        foreach ($this->errors as $fieldName => $fieldMessages)
        {
            if (is_array($fieldMessages)) {
                $errorMessages[] = $fieldName . ": " . implode(', ', $fieldMessages);
            } else {
                $errorMessages[] = $fieldName . ": " . $fieldMessages;
            }
        }
        return $errorMessages;
    }
}