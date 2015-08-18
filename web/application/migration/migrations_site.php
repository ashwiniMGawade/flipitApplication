<?php
require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();


$version = $locale = null;
$parms = isset($argv[1]) ? $argv[1] : null ;

if($parms) {
    $data = explode('-' , $parms);

    $locale = $data[0];
    $version = isset($data[1]) ? ($data[1] > 0) ? $data[1] : null  : null;
}

$loc ='' ;
if( isset($locale)) {
    $loc = $locale ."/" ;
}

defined('PUBLIC_LOCALE_PATH')
|| define('PUBLIC_LOCALE_PATH',
        dirname(dirname(dirname(__FILE__)))."/public/". $loc);

if(!file_exists(PUBLIC_LOCALE_PATH) && $locale != 'en'){

    die("This Locale does not exist! Try Another!!\n");
}


if(empty($locale)) {
    # cycle htoruh all site database
    foreach ($connections as $key => $connection) {
        # check database is being must be site
        if($key != 'imbull') {
            try {
                 migrateDatabase($connection['dsn'] , $key, $version);

            } catch (Exception $e) {

                echo $e->getMessage();
                echo "\n\n" ;
            }
        }
    }
}else {
    migrateDatabase($connections[$locale]['dsn'] , $locale,$version);
}




/**
 * migrateDatabase
 *
 * migrate the site datbase base on locale
 * @param string $dsn dsn string
 * @param struing $key locale
 */

function migrateDatabase($dsn, $key = "",$version = null)
{
    try {
        echo "Datbase: ".$dsn . "\n" ;

        # auto load doctrine library
        spl_autoload_register(array('Doctrine', 'autoload'));

        # create connection
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site'.$key);

        # auto  model class
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        # create donctrine manager
        $manager = Doctrine_Manager::getInstance();

        # set manager attribute like table class, base classes etc
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        # crate migration instance
        $migration = new Doctrine_Migration(realpath(APPLICATION_PATH.'/models/migrations_site'), $DMC );

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
}

