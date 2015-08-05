<?php
namespace Core\Domain\Factory;

use \Core\Domain\Usecase\System\SleepingInactiveVisitorsUsecase;
use \Core\Persistence\Factory\RepositoryFactory;

class SystemFactory
{
    public static function sleepInactiveVisitors()
    {
        return new SleepingInactiveVisitorsUsecase(RepositoryFactory::visitor());
    }
}
