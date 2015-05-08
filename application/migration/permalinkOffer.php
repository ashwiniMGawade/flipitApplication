<?php
set_time_limit ( 10000 );
ini_set('max_execution_time',115200);
ini_set("memory_limit","1024M");
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

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

$pattern = array ('/\s/','/[\.,+@#$%^&*!]+/');

$replace = array ("-","-");

$data = Doctrine_Query::create()->select('title,extendedUrl')->from('Offer')->fetchArray();

$newArray = array();
$i = 0;
foreach ($data as $d) {
    $input = strtolower(preg_replace ( $pattern, $replace, $d['title'] )); // UTF8 encoded
    $input = preg_replace("#[^A-Za-z1-9]#","_", $input);

        $updatePermalink = Doctrine_Core::getTable('Offer')->find($d['id']);
        $updatePermalink->extendedUrl = $input;
        $updatePermalink->save();

}

/*$newArray = Doctrine_Query::create()->select('extendedUrl')->from('Offer')->fetchArray();
//echo "<pre>"; print_r($newArray); die;
$deleteRp = Doctrine_Query::create()->delete('RoutePermalink')->where('type = "EXTOFFER"')->execute();
foreach ($newArray as $new){

    /*$getRoutePermalink = Doctrine_Query::create()->select()
    ->from('RoutePermalink')
    ->where('permalink = "'.$new['extendedUrl'].'"')
    ->fetchArray();

    if(count($getRoutePermalink) == 0){

        $updateRp = new RoutePermalink();
        $updateRp->permalink = $new['extendedUrl'];
        $updateRp->type = 'EXTOFFER';
        $updateRp->exactlink = 'offer/couponinfo/id/'.$new['id'];
        $updateRp->save();
    //}
}*/

echo 'done';
