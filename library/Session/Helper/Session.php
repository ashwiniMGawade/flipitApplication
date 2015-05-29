<?php
class Session_Helper_Session
{
    public static function getSessionTimeout()
    {
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $frontControllerObject = $application->getOption('resources');
        $sessionTimeout = isset($frontControllerObject['frontController']['params']['sessionTimeout'])
            ? $frontControllerObject['frontController']['params']['sessionTimeout']
            : 86400;
        return $sessionTimeout;
    }
}
