<?php

class Application_Admin_BaseController extends Zend_Controller_Action
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
         \Auth_StaffAdapter::checkACL();
    }
    public function setFlashMessage($messageType, $message)
    {
        $translatableSingleMessage = '';
        if (is_array($message)) {
            foreach ($message as $fieldMessage) {
                $translatableSingleMessage .=  $fieldMessage. '<br>';
            }
        } else {
            $translatableSingleMessage = $message;
        }
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $translatableSingleMessage = $this->view->translate($translatableSingleMessage);
        $flashMessenger->addMessage(array($messageType => $translatableSingleMessage));
    }

    public function rekeyObjects($data, $fieldName)
    {
        $formattedData = array();
        $getFunction = 'get'.$fieldName;
        foreach ($data as $record) {
            $formattedData[$record->$getFunction()] = $record;
        }
        return $formattedData;
    }

    public function dismount($object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }
}
