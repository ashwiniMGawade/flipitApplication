<?php
namespace Core\Cache\Factory;

use \Core\Cache\Service\Cache;
use \Core\Cache\Service\Memcached;

class CacheFactory
{
    public static function fastCache()
    {
        return new Cache(new Memcached());
    }
}
