<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\ApiKeyGenerator;
use \Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use \Core\Domain\Usecase\Admin\AddApiKeyUsecase;
use \Core\Domain\Usecase\Admin\DeleteApiKeyUsecase;
use \Core\Domain\Validator\ApiKeyValidator;
use \Core\Persistence\Factory\RepositoryFactory;

class AdminFactory
{
    public static function getApiKeys()
    {
        return new GetApiKeyListingUsecase(RepositoryFactory::apiKeys());
    }

    public static function createApiKey()
    {
        return new CreateApiKeyUsecase();
    }

    public static function addApiKey()
    {
        return new AddApiKeyUsecase(
            RepositoryFactory::apiKeys(),
            new ApiKeyValidator(new Validator()),
            new ApiKeyGenerator()
        );
    }

    public static function deleteApiKey()
    {
        return new DeleteApiKeyUsecase(RepositoryFactory::apiKeys());
    }
}
