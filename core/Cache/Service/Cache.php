<?php
namespace Core\Cache\Service;

use \Core\Cache\Adapter\CacheInterface;

class Cache implements CacheInterface
{
    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function set($key, $value, $lifeTime)
    {
        return $this->cache->set($key, $value, (int) $lifeTime);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function getMultiple(array $keys)
    {
        return $this->cache->getMulti($keys);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function flush()
    {
        return $this->cache->flush();
    }

    public function contains($key)
    {
        return (false !== $this->cache->get($key));
    }
}
