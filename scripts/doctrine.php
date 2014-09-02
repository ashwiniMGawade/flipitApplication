<?php

define('APPLICATION_ENV', 'development');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Doctrine and Symfony Classes
require_once 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH . '/../library');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony', APPLICATION_PATH . '/../library/Doctrine');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('KC\Entity', APPLICATION_PATH . '/../library/KC/Entity');
$classLoader->setNamespaceSeparator('_');
$classLoader->register();

// Zend Components
require_once 'Zend/Application.php';

// Create application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->getBootstrap()->bootstrap('doctrine');
$em = $application->getBootstrap()->getResource('doctrine');

// CODE FOR GENEREATE THE MODEL FROM YML FILE
use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\YamlDriver;

\Doctrine\ORM\Tools\Setup::registerAutoloadPEAR();
$config = new \Doctrine\ORM\Configuration();
$config->setProxyDir(APPLICATION_PATH . '/../library/KC/Proxy');
$config->setProxyNamespace('Proxies');
$config->setAutoGenerateProxyClasses((APPLICATION_ENV == "development"));
$driver = new YamlDriver(array(APPLICATION_PATH . "/../scripts/kc"));
 //$driver->setFileExtension('.yml');
$config->setMetadataDriverImpl($driver);

$paths = array(APPLICATION_PATH."/../scripts/kc");
$config = Setup::createYAMLMetadataConfiguration($paths, false);
$em = \Doctrine\ORM\EntityManager::create($em->getConnection(), $config);


$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));

\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet);