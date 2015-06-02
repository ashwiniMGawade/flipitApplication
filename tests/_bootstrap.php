<?php
// This is global bootstrap for autoloading 
define('APPLICATION_ENV', 'testing');
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

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
$isDevMode = true;
$config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);
$config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
$config->setAutoGenerateProxyClasses(true);
$config->setProxyNamespace('KC\Entity\Proxy');

// $connectionParamsLocale = array(
//     'driver'   => 'pdo_mysql',
//     'user'     => 'root',
//     'password' => 'root',
//     'dbname'   => 'flipit_test',
// );
$connectionParamsLocale = array(
    'driver'   => 'pdo_sqlite',
    'name'     => 'locale',
    'memory'   => true,
);

$em = EntityManager::create($connectionParamsLocale, $config);
\Codeception\Module\Doctrine2::$em = $em;
$mdFactory = $em->getMetadataFactory();
$classes = $mdFactory->getAllMetadata();
$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
$tool->dropDatabase();
$tool->createSchema($classes);
\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'_pages');
