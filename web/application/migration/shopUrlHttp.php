<?php
require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

$manager = Doctrine_Manager::getInstance();

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

CommonMigrationFunctions::loadDoctrineModels();

$array = array();
$newArray = array();
$data = Doctrine_Query::create()->select('actualUrl')->from('Shop')->fetchArray();

foreach($data as $j => $d) {
    $array[$j]['id'] = $d['id'];
    $array[$j]['url'] = parse_url($d['actualUrl']);
}


foreach ($array as $i => $arr){

    if(!array_key_exists('scheme', $arr['url'])){
        $newArray[$i]['id'] = $arr['id'];
        $newArray[$i]['url'] = 'http://'.$arr['url']['path'];
    }

}

foreach ($newArray as $data){
    $query = Doctrine_Core::getTable('Shop')->find($data['id']);
    $query->actualUrl = $data['url'];
    $query->save();
}

echo 'URL changes done';
die;
