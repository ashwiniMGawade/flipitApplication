<?php
namespace Core\Service\Errors;

/**
 * Interface ErrorsInterface
 *
 * @package Core\Service\Errors
 */
interface ErrorsInterface
{
    /**
     * @param $errorMessage
     * @param $fieldName
     *
     * @return mixed
     */
    public function setError($errorMessage, $fieldName = '');

    /**
     * @param array $errorMessages
     *
     * @return mixed
     */
    public function setErrors($errorMessages = array());

    /**
     * @param array $originalState
     *
     * @return mixed
     */
    public function setOriginalState($originalState);

    /**
     * @param array $fieldName
     *
     * @return mixed
     */
    public function getError($fieldName);

    /**
     * @return mixed
     */
    public function getErrorsAll();

    /**
     * @return mixed
     */
    public function getOriginalState();

    /**
     * @return mixed
     */
    public function getErrorMessages();
}
