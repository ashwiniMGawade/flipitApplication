<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Database\Service\DoctrineManager;
use \Core\Persistence\Database\Repository\PageRepository;
use \Core\Persistence\Database\Repository\ShopRepository;

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
}
