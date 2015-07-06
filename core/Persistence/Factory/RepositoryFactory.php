<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Database\Service\DoctrineManager;
use \Core\Persistence\Database\Repository\PageRepository;
use \Core\Persistence\Database\Repository\ApiKeyRepository;

class RepositoryFactory
{
    public static function page()
    {
        return new PageRepository((new DoctrineManager)->getLocaleEntityManger());
    }

    public static function apiKeys()
    {
        return new ApiKeyRepository((new DoctrineManager)->getUserEntityManger());
    }
}
