<?php
// use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
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
        # read dsn from config file an create new connection.
        $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        $key = strtolower($key);
        $connName = "dynamic_conn_" . $key;
        $dsn = $options['doctrine'][$key]['dsn'];

        # setup memcached
        $memcacheHostParams = $options['resources']['frontController']['params']['memcache'];
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
        $config->setProxyDir(APPLICATION_PATH . '/../library/KC/Entity/Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyNamespace('KC\Entity\Proxy');
        
        # create a new connection based on select dsn
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