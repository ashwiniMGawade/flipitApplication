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
        $paths = array(APPLICATION_PATH . '/../library/KC/Entity');
        $isDevMode = false;
        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        // registering noop annotation autoloader - allow all annotations by default
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
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
