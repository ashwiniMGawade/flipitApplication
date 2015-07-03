<?php 

namespace Tests;

class WebDatabaseHelperExtention extends \Codeception\Extension
{
    // list events to listen to
    public static $events = array(
        'test.after' => 'afterTest',
        'suite.before' => 'beforeSuite'
    );

    // methods that handle events
    public function afterTest(\Codeception\Event\TestEvent $e)
    {
        $this->databaseSetup();
    }

    public function beforeSuite(\Codeception\Event\SuiteEvent $e)
    {
        $this->databaseSetup();
    }

    public function databaseSetup()
    {
        $databaseHelper = new \Tests\WebDatabaseHelper;
        $applicationConfig = parse_ini_file(__DIR__."/../../web/application/configs/application.ini");

        $databases[] = $databaseHelper->getDatabaseCredentials($applicationConfig['doctrine.en.dsn']);
        $databases[] = $databaseHelper->getDatabaseCredentials($applicationConfig['doctrine.imbull']);

        foreach ($databases as $database) {
            
            $sqlDumpPath = 'tests/_data/' . $database['dbname'] . '.sql';
            
            // Drop and create database
            $databaseHelper
                ->connect('mysql:host=' . $database['host'] . ';', $database['user'], $database['password'])
                ->restart($database['dbname']);

            // Connect with the Database and import schema
            $databaseHelper
                ->connect('mysql:host=' . $database['host'] . ';dbname=' . $database['dbname'], $database['user'], $database['password']);

            if (file_exists($sqlDumpPath)) {
                $databaseHelper
                    ->load(file_get_contents($sqlDumpPath));
            } else {
                throw new \Exception("Sql dump can't be found. Looking for this file: " . $sqlDumpPath, 1);
            }
        }
    }
}
