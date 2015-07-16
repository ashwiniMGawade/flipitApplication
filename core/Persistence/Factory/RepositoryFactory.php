<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Database\Service\DoctrineManager;
use \Core\Persistence\Database\Repository\PageRepository;
use \Core\Persistence\Database\Repository\ShopRepository;
use \Core\Persistence\Database\Repository\ApiKeyRepository;
use \Core\Persistence\Database\Repository\AffliateNetworkRepository;

class RepositoryFactory
{
    public static function page()
    {
        return new PageRepository((new DoctrineManager)->getLocaleEntityManager());
    }

    public static function shop()
    {
        return new ShopRepository((new DoctrineManager)->getLocaleEntityManager());
    }

    public static function apiKeys()
    {
        return new ApiKeyRepository((new DoctrineManager)->getUserEntityManager());
    }

    public static function affliateNetwork()
    {
        return new AffliateNetworkRepository((new DoctrineManager)->getLocaleEntityManager());
    }
}
