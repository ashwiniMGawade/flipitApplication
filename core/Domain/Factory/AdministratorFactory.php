<?php
namespace Core\Domain\Factory;

use Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use Core\Domain\Usecase\Admin\GetShopUsecase;
use Core\Persistence\Factory\RepositoryFactory;

class AdministratorFactory
{
    public static function getApiKeys()
    {
        return new GetApiKeyListingUsecase(RepositoryFactory::apiKeys());
    }

    public static function getShop()
    {
        return new GetShopUsecase(RepositoryFactory::shop());
    }
}
