<?php
namespace Core\Persistence\Database\Service;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineLoad
{
    protected $localeEntityManger;
    protected $userEntityManger;
    
    /**
    *   Use this is Repository
    *
    *   $config = new \Core\Persistence\Database\Service\DoctrineLoad();
    *   $queryBuilder = $config->getLocaleEntityManger()->createQueryBuilder();
    */
    public function __construct()
    {
        $config = new AppConfig();
        $connection = $config->getConfigs();
        $splitMemcacheValues = explode(':', $connection['connections']['cacheParams']);
        $memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
        $memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
        $memcache = new \Memcached();
        $memcache->addServer($memcacheHost, $memcachePort);
        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcache);
        if ($connection['connections']['appMode'] === 'development' || $connection['connections']['appMode'] === 'testing') {
            $cache = $connection['connections']['cache'];
        }
        $config = Setup::createConfiguration($connection['connections']['isDevMode'], $connection['connections']['proxy_path'], $cache);
        $config->setProxyNamespace('Proxy');
        $driver = new AnnotationDriver(new AnnotationReader(), $connection['connections']['path']);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $this->localeEntityManger = EntityManager::create($connection['connections']['site'], $config);
        $this->userEntityManger = EntityManager::create($connection['connections']['user'], $config);
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
