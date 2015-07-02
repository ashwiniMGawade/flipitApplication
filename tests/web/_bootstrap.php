<?php
// Here you can initialize variables that will be available to your tests
require_once realpath(dirname(__FILE__) . '/../_data/fixtures.php');
require_once APPLICATION_PATH.'/../library/Doctrine/Common/ClassLoader.php';

// Cleaning the Db's
$applicationConfig = parse_ini_file(__DIR__."/../../web/application/configs/application.ini");
$flipitSiteDsn = $applicationConfig['doctrine.test.dsn'];
$flipitUserDsn = $applicationConfig['doctrine.user.dsn'];

// Get the Db's credentials in the loop.
// DBname, host, user, pass, dumppath
// $sqlDumpPath = 'tests/_data/flipit_test.sql';

$fixture = new \Tests\DatabaseHelper;
$a = $fixture->getDatabaseCredentials($flipitSiteDsn, 'tests/_data/flipit_test.sql');
$b = $fixture->getDatabaseCredentials($flipitUserDsn, 'tests/_data/flipit_test_user.sql');
$databases = array_merge($a, $b);
//print_r($databases); die;
foreach ($databases as $database) {
    $fixture->connect('mysql:host=' . $database['host'] . ';', $database['username'], $database['password'])->restart($database['name']);
    $fixture->connect('mysql:host=' . $database['host'] . ';dbname=' . $database['name'], $database['username'], $database['password']);

    if (file_exists($database['sqlDumpPath'])) {
        $fixture->load(file_get_contents($database['sqlDumpPath']));
    } else {
        throw new Exception("Sql dump can't be found", 1);
    }
}

// Setting up doctrine to be able to run fixtures.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../web/application'));

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

$paths = array(APPLICATION_PATH . '/../../core/Domain/Entity');
$isDevMode = true;
$config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

$connectionParamsUser = array(
    'driver'      => 'pdo_mysql',
    'user'        => 'root',
    'password' => 'root',
    'dbname'   => 'flipit_test_user',
    'host'  => "localhost",
);

$connectionParamsLocale = array(
    'driver'      => 'pdo_mysql',
    'user'        => 'root',
    'password' => 'root',
    'dbname'   => 'flipit_test',
    'host'  => "localhost",
);

$emLocale = EntityManager::create($connectionParamsLocale, $config);
$emUser = EntityManager::create($connectionParamsUser, $config);

$fixtures = new fixtures($emLocale, $emUser);
$fixtures->execute();

\Codeception\Util\Autoload::registerSuffix('Steps', __DIR__.DIRECTORY_SEPARATOR.'_steps');
\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'/../_pages');

// Configure doctrine2 module so cleanup works.
