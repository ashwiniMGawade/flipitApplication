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
    public function _before()
    {
        $databaseHelper = new DatabaseHelper;
        $databaseHelper->siteDatabaseSetup();
        $databaseHelper->userDatabaseSetup();
        $fixturesHelper = new FixturesHelper();
        $fixturesHelper->execute($this);
    }

    public function haveInDatabasePDOSite($table, $arr)
    {
        $databaseHelper = new DatabaseHelper;
        $databaseHelper->haveInDatabasePDOSite($table, $arr);
    }

    public function haveInDatabasePDOUser($table, $arr)
    {
        $databaseHelper = new DatabaseHelper;
        $databaseHelper->haveInDatabasePDOUser($table, $arr);
    }

    // public function initializeDb($moduleName, $database)
    // {
    //     $db = $this->getModule($moduleName);
    //     $db->_reconfigure($database);
    //     $db->_initialize();
    // }

    // public function flipitTestDb()
    // {
    //     return array(
    //         'dsn' => 'mysql:host=localhost;dbname=flipit_test',
    //         'dump' => 'tests/_data/flipit_test.sql',
    //         'user' => 'root',
    //         'password' => 'root',
    //         'cleanup' => false
    //     );
    // }

    // public function flipitTestUserDb()
    // {
    //     return array(
    //         'dsn' => 'mysql:host=localhost;dbname=flipit_test_user',
    //         'dump' => 'tests/_data/flipit_test_user.sql',
    //         'user' => 'root',
    //         'password' => 'root',
    //         'cleanup' => false
    //     );
    // }
}
