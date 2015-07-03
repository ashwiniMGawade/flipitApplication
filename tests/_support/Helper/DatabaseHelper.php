<?php 

namespace Helper;

class DatabaseHelper
{
    public function databaseSetup()
    {
        $databaseHelper = new PdoHelper;

        $application = new \Zend_Application(APPLICATION_ENV, __DIR__."/../../../web/application/configs/application.ini");
        $applicationConfig = $application->getOption('doctrine');

        $databases[] = $this->getDatabaseCredentials($applicationConfig['en']['dsn']);
        $databases[] = $this->getDatabaseCredentials($applicationConfig['imbull']);

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

    /**
     * Get Db credentials from the dsn of the application.ini
     * @param string $applicationDsn
     */
    private function getDatabaseCredentials($applicationDsn)
    {
        $splitDbName = explode('/', $applicationDsn);
        $splitDbUserName = explode(':', $splitDbName[2]);
        $splitDbPassword = explode('@', $splitDbUserName[1]);
        $splitHostName = explode('@', $splitDbUserName[1]);
        $dbPassword = $splitDbPassword[0];
        $dbUserName = $splitDbUserName[0];
        $dbName = $splitDbName[3];
        $hostName = isset($splitHostName[1]) ? $splitHostName[1] : 'localhost';
        return array(
            'host'     => $hostName,
            'driver'   => 'pdo_mysql',
            'user'     => $dbUserName,
            'password' => $dbPassword,
            'dbname'   => $dbName,
        );
    }
}
