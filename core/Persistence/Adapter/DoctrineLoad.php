<?php
namespace Core\Persistence\Adapter;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineLoad 
{
    protected $localeEntityManger;
    protected $userEntityManger;
    public function __construct($dbParamsLocale, $dbParamsUser, $appMode = 'development', $memcacheHostParams = 'localhost:11211')
    {
        $paths = array('/../../../../core/Domain/Entity', '/../../../../core/Domain/Entity/User');
        $splitMemcacheValues = explode(':', $memcacheHostParams);
        $memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
        $memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
        $memcache = new \Memcached();
        $memcache->addServer($memcacheHost, $memcachePort);
        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcache);
        $isDevMode = false;
        $proxyPath = null;
        if ($appMode === 'development' || $appMode === 'testing') {
            $cache = null;
            $isDevMode = true;
        }
        $config = Setup::createConfiguration($isDevMode, $proxyPath, $cache);
        $config->setProxyNamespace('Proxy');
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $this->localeEntityManger = EntityManager::create($dbParamsLocale, $config);
        $this->userEntityManger = EntityManager::create($dbParamsUser, $config);
    }

    public function getUserEntityManger()
    {
        return $this->userEntityManger;
    }

    public function getLocaleEntityManger()
    {
        return $this->localeEntityManger;
    }
}
