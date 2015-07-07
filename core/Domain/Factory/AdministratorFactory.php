<?php
namespace Core\Domain\Factory;

use Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use Core\Persistence\Factory\RepositoryFactory;

class AdministratorFactory
{
    public static function getApiKeys()
    {
        return new GetApiKeyListingUsecase(RepositoryFactory::apiKeys());
    }

    public static function addApiKey()
    {
        return new CreateApiKeyUsecase(RepositoryFactory::apiKeys());
    }
}
