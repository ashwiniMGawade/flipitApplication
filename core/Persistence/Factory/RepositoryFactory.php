<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Database\Service\DoctrineManager;
use \Core\Persistence\Database\Repository\PageRepository;

class RepositoryFactory
{
    public static function page()
    {
        $doctrineManager = new DoctrineManager();
        return new PageRepository($doctrineManager->getLocaleEntityManager());
    }
}
