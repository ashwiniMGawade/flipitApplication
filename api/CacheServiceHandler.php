<?php
use \Core\Domain\Service\Memcached;
use \Core\Persistence\Database\Service\AppConfig;

$cacheService = null;
switch ($app->config('cache.service')) {
    case 'memcached' :
    default:
        $cacheService = new Memcached(new AppConfig());
        break;
}
return $cacheService;