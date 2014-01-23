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
//$url = preg_replace ( $pattern, $replace, $url );

$data = Doctrine_Query::create()->select('name,permaLink')->from('Shop')->fetchArray();

$newArray = array();
$i = 0;
//echo "<pre>"; print_r($data); die;
foreach ($data as $d)
{
	
		$updatePermalink = Doctrine_Core::getTable('Shop')->find($d['id']);
		$updatePermalink->permaLink = strtolower(preg_replace ( $pattern, $replace, $d['name'] ));
		$updatePermalink->save();
	
}

$newArray = Doctrine_Query::create()->select('permaLink')->from('Shop')->fetchArray();

foreach ($newArray as $new){	

	/*$getRoutePermalink = Doctrine_Query::create()->select()
	->from('RoutePermalink')
	->where('permalink = "'.$new['permaLink'].'"')
	->fetchArray();
	
	if(count($getRoutePermalink) == 0){*/
		//echo $new['permaLink']."\n\r";
		$updateRp = new RoutePermalink();
		$updateRp->permalink = $new['permaLink'];
		$updateRp->type = 'SHP';
		$updateRp->exactlink = 'store/storedetail/id/'.$new['id'];
		$updateRp->save();
//	}
	
}

echo 'done';
?>