<?php

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH',
        dirname(dirname(__FILE__)));

defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                : 'production'));


//Ensure library/ is on include_path
set_include_path(
        implode(PATH_SEPARATOR,
                array(realpath(APPLICATION_PATH . '/../library'),
                        get_include_path(),)));
set_include_path(
        implode(PATH_SEPARATOR,
                array(realpath(DOCTRINE_PATH), get_include_path(),)));

/** Zend_Application */
//echo APPLICATION_PATH;
//echo LIBRARY_PATH;
//echo DOCTRINE_PATH;
//die;
require_once (LIBRARY_PATH . '/Zend/Application.php');
require_once(DOCTRINE_PATH . '/Doctrine.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');

$connections = $application->getOption('doctrine');

spl_autoload_register(array('Doctrine', 'autoload'));

$manager = Doctrine_Manager::getInstance();

//$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

$migration_user = new Doctrine_Migration(realpath(APPLICATION_PATH.'/models/migrations_user'), $DMC1 );
$version = isset($argv[1]) ? $argv[1] : null ;
$migration_user->migrate($version);

echo 'Database Migrated';
die;

/*echo "<p>Start generating migrations...</p>";


Doctrine_Core::generateMigrationsFromDb(realpath('C:/wamp/www/migrations'));


echo "<p>Migration classes added successfully.</p>";*/
