<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\Validator;
use \Core\Domain\Service\KeyGenerator;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use \Core\Domain\Usecase\Admin\AddApiKeyUsecase;
use \Core\Domain\Usecase\Admin\DeleteApiKeyUsecase;
use \Core\Domain\Usecase\Admin\GetVisitorListingUsecase;
use \Core\Domain\Usecase\Admin\GetShopUsecase;
use \Core\Domain\Usecase\Admin\CreateShopUsecase;
use \Core\Domain\Usecase\Admin\AddShopUsecase;
use \Core\Domain\Usecase\Admin\UpdateShopUsecase;
use \Core\Domain\Usecase\Admin\DeleteShopUsecase;
use \Core\Domain\Validator\ApiKeyValidator;
use \Core\Domain\Validator\ShopValidator;
use Core\Domain\Validator\VisitorValidator;
use \Core\Persistence\Factory\RepositoryFactory;
use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;

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

    public static function updateVisitors()
    {
        return new UpdateVisitorUsecase(RepositoryFactory::visitor(), new VisitorValidator(new Validator()));
    }

    public static function getShop()
    {
        return new GetShopUsecase(RepositoryFactory::shop(), new Purifier());
    }

    public static function createShop()
    {
        return new CreateShopUsecase();
    }

    public static function addShop()
    {
        return new AddShopUsecase(
            RepositoryFactory::shop(),
            new ShopValidator(new Validator()),
            RepositoryFactory::affliateNetwork(),
            new Purifier()
        );
    }

    public static function updateShop()
    {
        return new UpdateShopUsecase(
            RepositoryFactory::shop(),
            new ShopValidator(new Validator()),
            RepositoryFactory::affliateNetwork(),
            new Purifier()
        );
    }

    public static function deleteShop()
    {
        return new DeleteShopUsecase(RepositoryFactory::shop());
    }
}
