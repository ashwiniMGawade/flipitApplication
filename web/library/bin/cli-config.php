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
require_once realpath('../vendor/autoload.php');
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
}

$config = Setup::createConfiguration($isDevMode, $proxyPath, $cache);

$paths = array('../../core/Domain/Entity', '../../core/Domain/Entity/User');
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config->setProxyNamespace('Proxy');
$config->setMetadataDriverImpl($driver);
