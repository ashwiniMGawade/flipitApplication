<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
class BackEnd_Helper_DatabaseManager
{
    /**
    * create dynamic for chain management
    *  
    */
    public static function addConnection($key = 'be')
    {
        # read dsn from confiog file an dcreta new contien connection
        $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        $key = strtolower($key);
        $connName = "dynamic_conn_" . $key;
        $dsn = $options['doctrine'][$key]['dsn'];
        $paths = array(APPLICATION_PATH . '/../library/KC/Entity');
        $isDevMode = false;
        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        // registering noop annotation autoloader - allow all annotations by default
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        // set the proxy dir and set some options
        $config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyNamespace('KC\Entity\Proxy');
        # create a nrew connectoion based on select dsn
        $connectionParamsLocale = BootstrapDoctrineConnectionFunctions::getDatabaseCredentials($dsn);
        $emLocale = EntityManager::create($connectionParamsLocale, $config);
        \Zend_Registry::set('emLocale', $emLocale);
    }
    /**
     * Close databse connection
     * 
     */
    public static function closeConnection($conn = '')
    {
        $manager = \Zend_Registry::get('emLocale');
        $manager->getConnection()->close();
    }
}