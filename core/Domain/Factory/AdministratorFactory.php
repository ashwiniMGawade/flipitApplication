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
        return new CreateApiKeyUsecase();
    }

    /**
     * @return \Core\Domain\Usecase\Admin\AddApiKeyUsecase
     */
    public static function addApiKey()
    {
        return new AddApiKeyUsecase(
            RepositoryFactory::apiKeys(),
            new ApiKeyValidator(new Validator()),
            new ApiKeyGenerator()
        );
    }

    /**
     * @return \Core\Domain\Usecase\Admin\DeleteApiKeyUsecase
     */
    public static function deleteApiKey()
    {
        return new DeleteApiKeyUsecase(RepositoryFactory::apiKeys());
    }
}
