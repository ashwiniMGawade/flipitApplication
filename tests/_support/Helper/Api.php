<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function _before()
    {
        $databaseHelper = new DatabaseHelper;
        $databaseHelper->siteDatabaseSetup();
        $databaseHelper->userDatabaseSetup();
        $fixturesFactoryHelper = new FixturesFactoryHelper();
        $fixturesFactoryHelper->execute($this);
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
}
