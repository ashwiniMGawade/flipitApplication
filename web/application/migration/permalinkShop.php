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
//$url = preg_replace ( $pattern, $replace, $url );

$data = Doctrine_Query::create()->select('name,permaLink')->from('Shop')->fetchArray();

$newArray = array();
$i = 0;
foreach ($data as $d) {

        $updatePermalink = Doctrine_Core::getTable('Shop')->find($d['id']);
        $updatePermalink->permaLink = strtolower(preg_replace($pattern, $replace, $d['name']));
        $updatePermalink->save();

}

$newArray = Doctrine_Query::create()->select('permaLink')->from('Shop')->fetchArray();

foreach ($newArray as $new) {
    $updateRp = new RoutePermalink();
    $updateRp->permalink = $new['permaLink'];
    $updateRp->type = 'SHP';
    $updateRp->exactlink = 'store/storedetail/id/'.$new['id'];
    $updateRp->save();
}

echo 'done';
