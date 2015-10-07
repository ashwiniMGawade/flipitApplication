<?php
namespace Core\Domain\Factory;

use \Core\Domain\Usecase\System\GetApiKeyUsecase;
use \Core\Domain\Usecase\System\DeactivateSleepingVisitors;
use \Core\Domain\Usecase\System\GetSettingUsecase;
use \Core\Persistence\Factory\RepositoryFactory;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class SystemFactory
{
    public static function deactivateSleepingVisitors()
    {
        return new DeactivateSleepingVisitors(RepositoryFactory::visitor());
    }

    public static function getApiKey()
    {
        return new GetApiKeyUsecase(RepositoryFactory::apiKeys(), new Purifier());
    }

    public static function getSetting()
    {
        return new GetSettingUsecase(RepositoryFactory::settings(), new Purifier(), new Errors());
    }
}
