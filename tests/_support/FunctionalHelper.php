<?php
namespace Codeception\Module;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;
use \Doctrine\ORM\Tools\SchemaTool;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class FunctionalHelper extends \Codeception\Module
{
    public function initializeDb($moduleName, $database)
    {
        $db = $this->getModule($moduleName);
        $db->_reconfigure($database);
        $db->_initialize();
    }

    public function flipitTestDb()
    {
        return array(
            'user' => 'root',
            'password' => 'root',
            'dsn' => 'mysql:host=localhost;dbname=flipit_test',
            'dump' => '/tests/_data/flipit_test.sql',
            'populate' => true,
            'cleanup' => true,
            'repopulate' => true,
        );
    }

    public function flipitTestUserDb()
    {
        return array(
            'user' => 'root',
            'password' => 'root',
            'dsn' => 'mysql:host=localhost;dbname=flipit_test_user',
            'dump' => '/tests/_data/flipit_test_user.sql',
            'populate' => true,
            'cleanup' => true,
            'repopulate' => true,
        );
    }

    public function _afterSuite()
    {
        $this->initializeDb('Db', $this->flipitTestUserDb());
        $this->initializeDb('Db', $this->flipitTestDb());
    }
}
