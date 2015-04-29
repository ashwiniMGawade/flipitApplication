<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BootstrapDoctrineConnectionFunctions
{
    public static function doctrineConnections($doctrineOptions, $moduleDirectoryName, $localeCookieData)
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControllerObject = $application->getOption('resources');
        $memcacheHostParams = $frontControllerObject['frontController']['params']['memcache'];
        $splitMemcacheValues = explode(':', $memcacheHostParams);
        $memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
        $memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
        $memcache = new Memcached();
        $memcache->addServer($memcacheHost, $memcachePort);
        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcache);
        $annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
        $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
            $annotationReader,
            $cache
        );
        
        $paths = array(APPLICATION_PATH . '/../library/KC/Entity');
        $driver = new AnnotationDriver($cachedAnnotationReader, $paths);
        AnnotationRegistry::registerLoader('class_exists');

        $config = new Configuration();
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);
        $config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyNamespace('Proxy');
        $emUser = EntityManager::create(self::getDatabaseCredentials($doctrineOptions['imbull']), $config);
        $localSiteDbConnection = strtolower(self::getLocaleNameForDbConnection(
            $moduleDirectoryName,
            $localeCookieData
        ));
        $connectionParamsLocale = self::getDatabaseCredentials($doctrineOptions[$localSiteDbConnection]['dsn']);
        $emLocale = EntityManager::create($connectionParamsLocale, $config);
        Zend_Registry::set('emLocale', $emLocale);
        Zend_Registry::set('emUser', $emUser);
        BootstrapConstantsFunctions::constantsForLocaleAndTimezoneSetting();
        $localeValue = explode('_', COUNTRY_LOCALE);
        $localeValue = isset($localeValue[1]) ? $localeValue[1] : $localeValue[0];
        if (LOCALE == '') {
            date_default_timezone_set('Europe/Amsterdam');
        } else if (strtolower($localeValue) == LOCALE) {
            date_default_timezone_set(LOCALE_TIMEZONE);
        }
        return $emUser;
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
        } elseif (strlen($moduleDirectoryName) == 2 && HTTP_HOST=='www.kortingscode.nl') {
            $locale = 'en';
        }
        return $locale;
    }
}
