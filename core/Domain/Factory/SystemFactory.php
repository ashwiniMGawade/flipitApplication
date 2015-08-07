<?php
namespace Core\Domain\Factory;

use \Core\Domain\Usecase\System\DeactivateSleepingVisitors;
use \Core\Persistence\Factory\RepositoryFactory;

class SystemFactory
{
    public static function deactivateSleepingVisitors()
    {
        return new DeactivateSleepingVisitors(RepositoryFactory::visitor());
    }
}
