<?php
namespace Core\Persistence\Factory;

use Core\Persistence\Database\Repository\ApiKeyRepository;
use Core\Persistence\Database\Service\DoctrineLoad;

class FactoryRepository
{
    public static function getApiKeys()
    {
        $config = new DoctrineLoad();
        $queryBuilder = $config->getUserEntityManger()->createQueryBuilder();
        return new ApiKeyRepository($queryBuilder);
    }
}
