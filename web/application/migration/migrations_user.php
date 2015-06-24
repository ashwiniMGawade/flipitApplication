<?php

require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
spl_autoload_register(array('Doctrine', 'autoload'));


//$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

CommonMigrationFunctions::loadDoctrineModels();

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
