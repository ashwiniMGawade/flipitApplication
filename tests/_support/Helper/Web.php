<?php
namespace Helper;

/**
 * here you can define custom actions
 * all public methods declared in helper class will be available in $I
 */
class Web extends \Codeception\Module
{
    /**
     * This function is run before every web test to rest the Databases.
     * @param  \Codeception\TestCase $test 
     */
    public function _before(\Codeception\TestCase $test)
    {
        $databaseHelper = new \Tests\WebDatabaseHelper;

        $application = new \Zend_Application(APPLICATION_ENV, __DIR__."/../../../web/application/configs/application.ini");
        $applicationConfig = $application->getOption('doctrine');

        $databases[] = $databaseHelper->getDatabaseCredentials($applicationConfig['en']['dsn']);
        $databases[] = $databaseHelper->getDatabaseCredentials($applicationConfig['imbull']);

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
