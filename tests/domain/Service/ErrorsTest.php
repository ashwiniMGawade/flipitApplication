<?php
namespace Service;

use Core\Service\Errors;

class ErrorsTest extends \Codeception\TestCase\Test
{
    public function testSetErrorAndGetErrorMethods()
    {
        $errors = new Errors();
        $errors->setError('This value should not be blank.', 'test_field');
        $this->assertEquals('This value should not be blank.', $errors->getError('test_field'));
    }

    public function testTryToSetErrorWithoutFieldNameAndGetErrorsAll()
    {
        $errors = new Errors();
        $errors->setError('This value should not be blank.');
        $this->assertEquals(array('This value should not be blank.'), $errors->getErrorsAll());
    }

    public function testTryToSetAndGetMultipleErrors()
    {
        $errors = new Errors();
        $errorData = array(
                'field1' => 'error1',
                'field2' => 'error2'
        );
        $errors->setErrors($errorData);
        $this->assertEquals($errorData, $errors->getErrorsAll());
    }

    public function testTryToGetErrorMessagesForMultipleErrors()
    {
        $errors = new Errors();
        $errorData = array(
            'test_field' => 'This value should not be blank.'
        );
        $errors->setErrors($errorData);
        $this->assertEquals(array( 0 => 'test_field: This value should not be blank.'), $errors->getErrorMessages());
    }

    public function testGetErrorMethodWithInvalidInput()
    {
        $errors = new Errors();
        $errors->setError('test_field', 'This value should not be blank.');
        $this->assertEquals(null, $errors->getError('test_field_other'));
    }

    public function testGetErrorMessagesMethodWithAssigningMultipleErrorsOnSingleField()
    {
        $errors = new Errors();
        $errorData = array(
            'test_field' => array( 'This value should not be blank', 'This value should be greater than 10')
        );
        $errors->setErrors($errorData);
        $this->assertEquals(array( 0 => 'test_field: This value should not be blank, This value should be greater than 10'), $errors->getErrorMessages());
    }
}
