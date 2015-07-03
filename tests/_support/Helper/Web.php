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
        $databaseHelper->databaseSetup();
    }
}
