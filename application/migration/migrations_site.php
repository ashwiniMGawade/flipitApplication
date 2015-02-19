<?php


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
                 migrateDatabase($connection['dsn'] , $key,$version);

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


        # create coonection
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site'.$key);
        //$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

        # auto  model class
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        # cretae donctrine mager
        $manager = Doctrine_Manager::getInstance();

        # set manager attribute like table class, base classes etc
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        # crate migration instance
        $migration = new Doctrine_Migration(realpath(APPLICATION_PATH.'/models/migrations_site'), $DMC );


        echo $migration->getLatestVersion() . "\n";

        echo $migration->getNextVersion() . "\n";
        echo $migration->getNextMigrationClassVersion() . "\n";
        echo $migration->getCurrentVersion() . "\n";

        if($migration->getLatestVersion() > $migration->getCurrentVersion() ) {
            # execute migrate()
            $migration->migrate($version);
            echo "Database has been Migrated successfully \n\n";
        } else {
            echo "Database has been Migrated successfully \n\n";
        }

        #close connection
        $manager->closeConnection($DMC);

    } catch (Exception $e) {

         var_dump($e->getMessage());
    }

}

/*echo "<p>Start generating migrations...</p>";
Doctrine_Core::generateMigrationsFromDb(realpath('C:/wamp/www/migrations'));
echo "<p>Migration classes added successfully.</p>";*/
