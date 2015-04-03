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
    public function databaseSwitch($databaseType = "")
    {
        \Codeception\Module\Doctrine2::$em = "";
        $paths = array(APPLICATION_PATH . '/../library/KC/Entity');
        $isDevMode = true;
        $config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyNamespace('KC\Entity\Proxy');

        $connectionParamsLocale = array(
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => 'password',
            'dbname'   => 'flipit_test'.$databaseType,
        );
        $em = EntityManager::create($connectionParamsLocale, $config);
        // $test = new \Codeception\Module\Doctrine2();
        // $test->config(array('cleanup' => true, 'auto_connect' => true));
        \Codeception\Module\Doctrine2::$em = $em;
    }
}
