<?php
namespace Core\Domain\Service;

use \Core\Domain\Adapter\CacheInterface;
use \Core\Persistence\Database\Service\AppConfig;

class Memcached implements CacheInterface
{
    protected $memcached;

    public function __construct(AppConfig $appConfig)
    {
        $connectionInformation = $appConfig->getConfigs();
        $splitMemcacheValues = explode(':', $connectionInformation['connections']['cacheParams']);
        $memcachePort = isset($splitMemcacheValues[1]) ? $splitMemcacheValues[1] : '';
        $memcacheHost = isset($splitMemcacheValues[0]) ? $splitMemcacheValues[0] : '';
        $this->memcached = new \Memcached();
        $this->memcached->addServer($memcacheHost, $memcachePort);
    }

    public function set($key, $value, $lifeTime)
    {
        return $this->memcached->set($key, $value, (int) $lifeTime);
    }

    public function get($key)
    {
        return $this->memcached->get($key);
    }

    public function getMultiple(array $keys)
    {
        return $this->memcached->getMulti($keys);
    }

    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    public function flush()
    {
        return $this->memcached->flush();
    }

    public function contains($key)
    {
        return (false !== $this->memcached->get($key));
    }
}
