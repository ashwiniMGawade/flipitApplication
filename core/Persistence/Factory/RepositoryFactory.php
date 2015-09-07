<?php
namespace Core\Persistence\Factory;

use Core\Persistence\Database\Repository\OfferRepository;
use Core\Persistence\Database\Repository\ViewCountRepository;
use Core\Persistence\Database\Repository\VisitorRepository;
use \Core\Persistence\Database\Service as Service;
use \Core\Persistence\Database\Repository\PageRepository;
use \Core\Persistence\Database\Repository\ShopRepository;
use \Core\Persistence\Database\Repository\ApiKeyRepository;
use \Core\Persistence\Database\Repository\AffliateNetworkRepository;
use \Core\Persistence\Database\Repository\WidgetRepository;

class RepositoryFactory
{
    public static function page()
    {
        return new PageRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function shop()
    {
        return new ShopRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function apiKeys()
    {
        return new ApiKeyRepository((new Service\DoctrineManager(new Service\AppConfig()))->getUserEntityManager());
    }

    public static function visitor()
    {
        return new VisitorRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function affliateNetwork()
    {
        return new AffliateNetworkRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function viewCount()
    {
        return new ViewCountRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function widget()
    {
        return new WidgetRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function offer()
    {
        return new OfferRepository((new DoctrineManager)->getLocaleEntityManager());
    }
}
