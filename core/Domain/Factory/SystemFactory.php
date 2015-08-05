<?php
namespace Core\Domain\Factory;

use \Core\Domain\Usecase\System\GetApiKeyUsecase;
use \Core\Persistence\Factory\RepositoryFactory;

class SystemFactory
{
    public static function getApiKey()
    {
        return new GetApiKeyUsecase(RepositoryFactory::apiKeys());
    }
}
