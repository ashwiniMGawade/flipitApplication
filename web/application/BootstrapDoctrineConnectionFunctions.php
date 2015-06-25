<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\Setup;
class BootstrapDoctrineConnectionFunctions
{
    public static function doctrineConnections($doctrineOptions, $moduleDirectoryName, $localeCookieData)
    {
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $frontControllerObject = $application->getOption('resources');
        $config = self::setMemcachedAndProxyClasses($frontControllerObject);
        $userDSN = Core\Persistence\Database\Service\DatabaseConnection::getDsn('imbull');
        if (APPLICATION_ENV == 'testing') {
            if (APPLICATION_ENV_FUNCTIONAL == 'testing_functional') {
                $emUser = EntityManager::create(self::getDatabaseCredentials($userDSN), $config);
            } else {
                $emUser = \Codeception\Module\Doctrine2::$em;
            }
        } else {
            $emUser = EntityManager::create(self::getDatabaseCredentials($userDSN), $config);
        }
        $localSiteDbConnection = strtolower(self::getLocaleNameForDbConnection($moduleDirectoryName, $localeCookieData));
        $localeDSN = Core\Persistence\Database\Service\DatabaseConnection::getDsn($localSiteDbConnection);
        self::setEntityManagerForlocale($localeDSN, $config);
        Zend_Registry::set('emUser', $emUser);
        BootstrapConstantsFunctions::constantsForLocaleAndTimezoneSetting();
        self::setDefaultTimezone();
        return $emUser;
    }
    
    public static function setMemcachedAndProxyClasses($frontControllerObject)
    {
        $memcacheHostParams = $frontControllerObject['frontController']['params']['memcache'];
        $splitMemcacheValues = explode(':', $memcacheHostParams);
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
        $config->setProxyNamespace('Proxy');

        $paths = array('/../../core/Domain/Entity', APPLICATION_PATH . '/../../core/Domain/Entity/User');
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        return $config;
    }

    public static function setEntityManagerForlocale($dsn, $config)
    {
        $databaseConnectionCredentials = self::getDatabaseCredentials($dsn);
        if (APPLICATION_ENV == 'testing') {
            if (APPLICATION_ENV_FUNCTIONAL == 'testing_functional') {
                $emLocale = EntityManager::create($databaseConnectionCredentials, $config);
            } else {
                $emLocale =  \Codeception\Module\Doctrine2::$em;
            }
        } else {
            $emLocale =  EntityManager::create($databaseConnectionCredentials, $config);
        }
        Zend_Registry::set('emLocale', $emLocale);
    }

    public static function setDefaultTimezone()
    {
        $localeValue = explode('_', COUNTRY_LOCALE);
        $localeValue = isset($localeValue[1]) ? $localeValue[1] : $localeValue[0];
        if (LOCALE == '') {
            date_default_timezone_set('Europe/Amsterdam');
        } else if (strtolower($localeValue) == LOCALE) {
            date_default_timezone_set(LOCALE_TIMEZONE);
        }
    }

    public static function getDatabaseCredentials($doctrineOptions)
    {
        $splitDbName = explode('/', $doctrineOptions);
        $splitDbUserName = explode(':', $splitDbName[2]);
        $splitDbPassword = explode('@', $splitDbUserName[1]);
        $splitHostName = explode('@', $splitDbUserName[1]);
        $dbPassword = $splitDbPassword[0];
        $dbUserName = $splitDbUserName[0];
        $dbName = $splitDbName[3];
        $hostName = isset($splitHostName[1]) ? $splitHostName[1] : 'localhost';
        return array(
            'host'     => $hostName,
            'driver'   => 'pdo_mysql',
            'user'     => $dbUserName,
            'password' => $dbPassword,
            'dbname'   => $dbName,
        );
    }

    public static function getLocaleNameForDbConnection($moduleDirectoryName, $localeCookieData)
    {
        $locale = 'en';
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        if (strlen($moduleDirectoryName) == 2 && HTTP_HOST== $httpScheme.'.flipit.com') {
            $locale = $moduleDirectoryName;
        } elseif ($moduleDirectoryName == 'admin') {
            $locale = isset($localeCookieData) ? $localeCookieData : 'en';
        } elseif ($moduleDirectoryName == "default") {
            $locale = 'en';
        } elseif (strlen($moduleDirectoryName) == 2 && HTTP_HOST== $httpScheme.'.kortingscode.nl') {
            $locale = 'en';
        }
        return $locale;
    }
}
