<?php
namespace Core\Persistence\Factory;

use Core\Persistence\Database\Repository\VisitorRepository;
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

<<<<<<< HEAD
    public static function visitor()
    {
        return new VisitorRepository((new DoctrineManager)->getLocaleEntityManager());
=======
    public static function affliateNetwork()
    {
        return new AffliateNetworkRepository((new DoctrineManager)->getLocaleEntityManager());
>>>>>>> 381aaf1e0b76a8def95760350d42f53c4cf96f72
    }
}
