<?php
namespace Core\Domain\Factory;

use Core\Domain\Service\ApiKeyGenerator;
use Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use Core\Persistence\Factory\RepositoryFactory;

/**
 * Class AdministratorFactory
 *
 * @package Core\Domain\Factory
 */
class AdministratorFactory
{
    /**
     * @return \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase
     */
    public static function getApiKeys()
    {
        return new GetApiKeyListingUsecase(RepositoryFactory::apiKeys());
    }

    /**
     * @return \Core\Domain\Usecase\Admin\CreateApiKeyUsecase
     */
    public static function createApiKey()
    {
        return new CreateApiKeyUsecase(RepositoryFactory::apiKeys());
    }

    /**
     * @return \Core\Domain\Service\ApiKeyGenerator
     */
    public static function apiKey()
    {
        return new ApiKeyGenerator();
    }
}
