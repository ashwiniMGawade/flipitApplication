<?php
require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

CommonMigrationFunctions::loadDoctrineModels();

$pattern = array ('/\s/','/[\.,+@#$%^&*!]+/');

$replace = array ("-","-");

$data = Doctrine_Query::create()->select('name,permalink')->from('Category')->fetchArray();
//echo "<pre>"; print_r($data); die;
$newArray = array();
$i = 0;
foreach ($data as $d) {
        $updatePermalink = Doctrine_Core::getTable('Category')->find($d['id']);
        $updatePermalink->permaLink = strtolower(preg_replace($pattern, $replace, $d['name']));
        $updatePermalink->save();
}

echo 'done';
die;
