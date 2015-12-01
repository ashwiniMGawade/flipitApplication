<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Service\Cache;
use \Core\Persistence\Service\Memcached;

class CacheFactory
{
    public static function keyValueCache()
    {
        return new Cache(new Memcached());
    }
}
