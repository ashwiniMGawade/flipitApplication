<?php
class Application_Form_DateGreaterThanToday extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'dateInvalid';
    protected $_messageTemplates = array(
        self::DATE_INVALID => "'%value%' is not greater than or equal today"
    );

    public function isValid($value)
    {
        echo $this->_setValue($value);
        echo $today = date('Y-m-d'); die;
        // expecting $value to be YYYY-MM-DD
        if ($value < $today) {
            $this->_error(self::DATE_INVALID);
            return false;
        }
        return true;
    }
}
