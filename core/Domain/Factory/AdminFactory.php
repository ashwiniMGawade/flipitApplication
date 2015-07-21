<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\KeyGenerator;
use \Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use \Core\Domain\Usecase\Admin\AddApiKeyUsecase;
use \Core\Domain\Usecase\Admin\DeleteApiKeyUsecase;
use \Core\Domain\Usecase\Admin\GetVisitorListingUsecase;
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
            new KeyGenerator()
        );
    }

    public static function deleteApiKey()
    {
        return new DeleteApiKeyUsecase(RepositoryFactory::apiKeys());
    }

    public static function getVisitors()
    {
        return new GetVisitorListingUsecase(RepositoryFactory::visitor());
    }
}
