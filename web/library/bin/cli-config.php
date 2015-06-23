<?php

/*
Needs a massive refactor!! Needs to use BootstrapDoctrineConnectionFunctions to make it DRY. 
Also the ENV is not working as that is not set in the commandline.
 */

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

// Define default application environment
defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV'): 'production'));

// Get Memcached settings from application.ini
require_once '/../../vendor/zendframework/zendframework1/library/Zend/Config/Ini.php';
$config = new Zend_Config_Ini('../application/configs/application.ini', APPLICATION_ENV);
$memcacheEndpoint = $config->resources->frontController->params->memcache;
$splitMemcacheValues = explode(':', $memcacheEndpoint);
$memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
$memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
$memcache = new Memcached();
$memcache->addServer($memcacheHost, $memcachePort);
$cache = new \Doctrine\Common\Cache\MemcachedCache;
$cache->setMemcached($memcache);
$isDevMode = false;
$proxyPath = null;
if (APPLICATION_ENV == 'development') {
    $cache = null;
    $isDevMode = true;
    $proxyPath = APPLICATION_PATH . '/../library/KC/Entity/Proxy';
}

$config = Setup::createConfiguration($isDevMode, $proxyPath, $cache);

$paths = array('../library/KC/Entity');
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config->setProxyNamespace('Proxy');
$config->setMetadataDriverImpl($driver);

$dsn = array(
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' =>
    'root', 'dbname' => 'flipit_in'
);
$em = EntityManager::create($dsn, $config);
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
