<?php
class Application_Form_DateGreaterThanToday extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'dateInvalid';
    protected $_messageTemplates = array(
        self::DATE_INVALID => ""
    );
    public function isValid($dateInputByUser)
    {
        $dateInputByUser =  strtotime($dateInputByUser);
        $currentDate = strtotime(date('d-m-Y'));
        if ($dateInputByUser < $currentDate) {
            $this->_error(self::DATE_INVALID);
            return false;
        }
        return true;
    }
}
