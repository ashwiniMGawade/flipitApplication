<?php

require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

try {
    CommonMigrationFunctions::setTimeAndMemoryLimit();

    $connections = CommonMigrationFunctions::getAllConnectionStrings();
    $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
    spl_autoload_register(array('Doctrine', 'autoload'));

    $DMC = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

    CommonMigrationFunctions::loadDoctrineModels();

    $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
    $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
    $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

    $migration = new Doctrine_Migration(realpath(APPLICATION_PATH.'/models/migrations_user'), $DMC );
    $version = isset($argv[1]) ? $argv[1] : null ;

    // echo $migration->getLatestVersion() . "\n";
    // echo $migration->getNextVersion() . "\n";
    // echo $migration->getNextMigrationClassVersion() . "\n";
    // echo $migration->getCurrentVersion() . "\n";

    if($migration->getLatestVersion() > $migration->getCurrentVersion() ) {
        # execute migrate()
        $migration->migrate($version);
        echo "Database has been Migrated successfully to v" . $migration->getCurrentVersion() . " \n\n";
    } else {
        echo "No migrations available. \n\n";
    }

    #close connection
    $manager->closeConnection($DMC);

} catch (Exception $e) {
    print_r($e->getMessage());
}
