<?php
namespace Core\Persistence\Database\Service;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineManager
{
    protected $localeEntityManger;
    protected $userEntityManger;
    
    public function __construct(AppConfigInterface $appConfig)
    {
        $connectionInformation = $appConfig->getConfigs();
        $splitMemcacheValues = explode(':', $connectionInformation['connections']['cacheParams']);
        $memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
        $memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
        $memcache = new \Memcached();
        $memcache->addServer($memcacheHost, $memcachePort);
        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcache);
        if ($connectionInformation['connections']['appMode'] === 'development' || $connectionInformation['connections']['appMode'] === 'testing') {
            $cache = $connectionInformation['connections']['cache'];
        }
        $config = Setup::createConfiguration($connectionInformation['connections']['isDevMode'], $connectionInformation['connections']['proxy_path'], $cache);
        $config->setProxyNamespace('Proxy');
        $driver = new AnnotationDriver(new AnnotationReader(), $connectionInformation['connections']['path']);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $this->localeEntityManger = EntityManager::create($connectionInformation['connections']['site'], $config);
        $this->userEntityManger = EntityManager::create($connectionInformation['connections']['user'], $config);
    }

    public function getUserEntityManager()
    {
        return $this->userEntityManger;
    }

    public function getLocaleEntityManager()
    {
        return $this->localeEntityManger;
    }
}
