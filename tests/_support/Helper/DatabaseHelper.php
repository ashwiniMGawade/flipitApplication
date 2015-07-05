<?php 

namespace Helper;

class DatabaseHelper
{
    protected $siteConfig;
    protected $userConfig;

    public function __construct()
    {
        $this->siteConfig = $this->getDatabaseCredentials("mysql://root:root@localhost/flipit_test");
        $this->userConfig = $this->getDatabaseCredentials("mysql://root:root@localhost/flipit_test_user");
    }

    public function haveInDatabasePDOSite($table, $arr)
    {
        $this->insertInToDb($this->siteConfig, $table, $arr);
    }

    public function haveInDatabasePDOUser($table, $arr)
    {
        $this->insertInToDb($this->siteConfig, $table, $arr);
    }

    public function siteDatabaseSetup()
    {
        $this->databaseSetup($this->siteConfig);
    }

    public function userDatabaseSetup()
    {
        $this->databaseSetup($this->userConfig);
    }

    private function insertInToDb($config, $table, $arr)
    {
        $pdoHelper = new PdoHelper;
        $pdoHelper
            ->connect(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'],
                $config['user'],
                $config['password']
            )
            ->insertInToDb($table, $arr);
    }

    private function databaseSetup($config)
    {
        $verifiedSqlDumpPath = $this->getSqlDump($config['dbname']);

        $pdoHelper = new PdoHelper;

        $pdoHelper
            ->connect(
                'mysql:host=' . $config['host'] . ';',
                $config['user'],
                $config['password']
            )
            ->restart($config['dbname']);

        $pdoHelper
            ->connect(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'],
                $config['user'],
                $config['password']
            )
            ->load(file_get_contents($verifiedSqlDumpPath));
    }

    private function getSqlDump($databaseName)
    {
        $sqlDumpPath = 'tests/_data/' . $databaseName . '.sql';

        if (file_exists($sqlDumpPath)) {
            return $sqlDumpPath;
        } else {
            throw new \Exception("Sql dump can't be found. Looking for this file: " . $sqlDumpPath, 1);
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
