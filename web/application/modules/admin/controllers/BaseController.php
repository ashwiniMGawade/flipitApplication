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

    public function uploadImage($file, $uploadPath)
    {
        $rootPath = ROOT_PATH . $uploadPath;
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0755, true);
        } elseif (!is_writable($rootPath)) {
            chmod($rootPath, 0755);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png,JPG,PNG', true));
        $imageName = pathinfo($adapter->getFileName($file, false));
        $imageName = isset($imageName['extension']) ? time().'.'.$imageName['extension'] : '';
        $targetPath = $rootPath . $imageName;
        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array('target' => $targetPath, 'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return $imageName;
        } else {
            return false;
        }
    }
}
