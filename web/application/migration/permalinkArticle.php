<?php
require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

CommonMigrationFunctions::loadDoctrineModels();

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

Doctrine_Core::loadModels(APPLICATION_PATH . '/models');


$pattern = array ('/\s/','/[\.,+@#$%^&*!]+/');

$replace = array ("-","-");

$data = Doctrine_Query::create()->select('title,permalink')->from('Articles')->fetchArray();
//echo "<pre>"; print_r($data); die;
$newArray = array();
$i = 0;
foreach ($data as $d) {
        $updatePermalink = Doctrine_Core::getTable('Articles')->find($d['id']);
        $updatePermalink->permalink = strtolower(preg_replace ( $pattern, $replace, $d['title'] ));
        $updatePermalink->save();
}

echo 'done';
die;
