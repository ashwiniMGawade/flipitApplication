<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BootstrapDoctrineConnectionFunctions
{
    public static function doctrineConnections($doctrineOptions, $moduleDirectoryName, $localeCookieData)
    {
        // globally used cache driver, in production use APC or memcached
        if (APPLICATION_ENV == "development") {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }
          // standard annotation reader
        $annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
        $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
            $annotationReader, // use reader
            $cache // and a cache driver
        );
        

        $paths = array(APPLICATION_PATH . '/../library/KC/Entity');
        $isDevMode = false;
        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver($cachedAnnotationReader, $paths);
        // registering noop annotation autoloader - allow all annotations by default
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // set the proxy dir and set some options
        $config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyNamespace('Proxy');


        // db connection parameters for user(imbull) database
        $emUser = EntityManager::create(self::getDatabaseCredentials($doctrineOptions['imbull']), $config);
        // get locale name
        $localSiteDbConnection = strtolower(self::getLocaleNameForDbConnection(
            $moduleDirectoryName,
            $localeCookieData
        ));
        // db connection parameters for locale database
        $connectionParamsLocale = self::getDatabaseCredentials($doctrineOptions[$localSiteDbConnection]['dsn']);

        $emLocale = EntityManager::create($connectionParamsLocale, $config);
        Zend_Registry::set('emLocale', $emLocale);
        Zend_Registry::set('emUser', $emUser);
        BootstrapConstantsFunctions::constantsForLocaleAndTimezoneSetting();
        $localeValue = explode('_', COUNTRY_LOCALE);
        if (LOCALE == '') {
            date_default_timezone_set('Europe/Amsterdam');
        } else if (strtolower($localeValue[1]) == LOCALE) {
            date_default_timezone_set(LOCALE_TIMEZONE);
        }
        return $emUser;
    }

    public static function getDatabaseCredentials($doctrineOptions)
    {
        $splitDbName = explode('/', $doctrineOptions);
        $splitDbUserName = explode(':', $splitDbName[2]);
        $splitDbPassword = explode('@', $splitDbUserName[1]);
        $dbPassword = $splitDbPassword[0];
        $dbUserName = $splitDbUserName[0];
        $dbName = $splitDbName[3];
        return array(
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
