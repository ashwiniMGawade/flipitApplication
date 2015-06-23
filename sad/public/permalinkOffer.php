<?php
set_time_limit ( 10000 );
ini_set('max_execution_time',115200);
ini_set("memory_limit","1024M");

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH',
		realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');

require_once(DOCTRINE_PATH.'/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

$DMC = Doctrine_Manager::connection('mysql://imbull:imbull2012dfr@localhost/kortingscode_site', 'doctrine_site');


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
foreach ($data as $d)
{
	$input = strtolower(preg_replace ( $pattern, $replace, $d['title'] )); // UTF8 encoded
	$input = preg_replace("#[^A-Za-z1-9]#","_", $input);
	
		$updatePermalink = Doctrine_Core::getTable('Offer')->find($d['id']);
		$updatePermalink->extendedUrl = $input;
		$updatePermalink->save();
	
}

echo 'done';
?>