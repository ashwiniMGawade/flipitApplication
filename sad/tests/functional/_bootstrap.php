<?php
// This is global bootstrap for autoloading 
define('APPLICATION_ENV_FUNCTIONAL', 'testing_functional');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'testing');
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
require_once realpath(dirname(__FILE__) . '/../_data/fixtures.php');
require_once APPLICATION_PATH.'/../library/Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH . '/../library');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony', APPLICATION_PATH . '/../library/Doctrine');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('KC', APPLICATION_PATH . '/../library');
$classLoader->register();

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;
use \Doctrine\ORM\Tools\SchemaTool;

$paths = array(APPLICATION_PATH . '/../library/KC/Entity');
$isDevMode = false;
$config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);
$connectionParamsLocale = array(
    'driver'      => 'pdo_mysql',
    'user'        => 'root',
    'password' => 'root',
    'dbname'   => 'flipit_test',
    'host'	=> "localhost",
);
$em = EntityManager::create($connectionParamsLocale, $config);
\Codeception\Module\Doctrine2::$em = $em;
/*$metaDataFactory = $em->getMetadataFactory();
$classes = $metaDataFactory->getAllMetadata();
$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
$tool->dropDatabase();
$tool->createSchema($classes);
$fixtures = new fixtures($em);
$fixtures->execute();*/
\Codeception\Util\Autoload::registerSuffix('Steps', __DIR__.DIRECTORY_SEPARATOR.'_steps');