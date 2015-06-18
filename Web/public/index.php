<?php

defined('APPLICATION_PATH')
    || define(
        'APPLICATION_PATH',
        realpath(dirname(__FILE__) . '/../application')
    );

defined('LIBRARY_PATH')
        || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');
 
// Sets the environment to testing for codeception
if (isset($_SERVER['HTTP_USER_AGENT']) && ($_SERVER['HTTP_USER_AGENT'] == 'Symfony2 BrowserKit' || strpos($_SERVER['HTTP_USER_AGENT'], 'PhantomJS') == true)) {
    define('APPLICATION_ENV', 'testing');
}

// Define default application environment
defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV'): 'production'));

//Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../vendor/zendframework/zendframework1/library')
        )
    )
);
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(realpath(DOCTRINE_PATH), get_include_path())
    )
);
require_once realpath(APPLICATION_PATH . '/../vendor/autoload.php');
/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'PHPExcel/PHPExcel.php';

$requestUri = $_SERVER['REQUEST_URI'];
if (preg_match('/admin/', $requestUri, $matches)) {
    //require_once APPLICATION_PATH.'/services/Ipaddress/Ipaddress.php';
}

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();

