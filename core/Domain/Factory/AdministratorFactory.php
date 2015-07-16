<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Usecase\Admin\GetShopUsecase;
use \Core\Domain\Usecase\Admin\CreateShopUsecase;
use \Core\Domain\Usecase\Admin\AddShopUsecase;
use \Core\Persistence\Factory\RepositoryFactory;
use \Core\Domain\Validator\ShopValidator;

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

    public static function createShop()
    {
        return new CreateShopUsecase();
    }

    public static function addShop()
    {
        return new AddShopUsecase(
            RepositoryFactory::shop(),
            new ShopValidator(new Validator()),
            RepositoryFactory::affliateNetwork()
        );
    }
}
