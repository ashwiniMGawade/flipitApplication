<?php
class Application_Service_Session_Timeout
{
    public static function getSessionTimeout()
    {
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $frontControllerObject = $application->getOption('resources');
        $sessionTimeout = isset($frontControllerObject['frontController']['params']['sessionTimeout'])
            ? $frontControllerObject['frontController']['params']['sessionTimeout']
            : 64800;
        return $sessionTimeout;
    }
}
