<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\Memcached;
use \Core\Persistence\Database\Service\AppConfig;

class CacheFactory
{
    public static function fastCache()
    {
        return new Memcached(new AppConfig());
    }
}
