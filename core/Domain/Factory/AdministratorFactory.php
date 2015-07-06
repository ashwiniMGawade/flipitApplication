<?php
namespace Core\Domain\Factory;

use Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use Core\Persistence\Factory\RepositoryFactory;

class AdministratorFactory
{
    public static function getApiKeys()
    {
        return new GetApiKeyListingUsecase(RepositoryFactory::apiKeys());
    }
}
