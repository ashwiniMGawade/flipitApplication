<?php
namespace Core\Cache\Adapter;

interface CacheInterface
{
    public function set($key, $value, $lifeTime);
    public function get($key);
    public function getMultiple(array $keys);
    public function delete($key);
    public function flush();
    public function contains($key);
}