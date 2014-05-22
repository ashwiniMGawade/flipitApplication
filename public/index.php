<?php
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH',
                realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARY_PATH')
        || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV',
                (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                        : 'production'));

$webSiteOfUser =  null;
//Ensure library/ is on include_path
set_include_path(
        implode(PATH_SEPARATOR,
                array(realpath(APPLICATION_PATH . '/../library'),
                        get_include_path(),)));
set_include_path(
        implode(PATH_SEPARATOR,
                array(realpath(DOCTRINE_PATH), get_include_path(),)));
/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'PHPExcel/PHPExcel.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');
require_once LIBRARY_PATH.'/FrontEnd/Helper/lessCssHelper.php';
$application->bootstrap()->run();
