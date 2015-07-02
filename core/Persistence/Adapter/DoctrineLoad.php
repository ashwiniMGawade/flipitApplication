<?php
namespace Core\Persistence\Adapter;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineLoad
{
    protected $localeEntityManger;
    protected $userEntityManger;
    public function __construct($dbParamsLocale, $dbParamsUser)
    {
        $paths = array('/../../../../core/Domain/Entity');
        $isDevMode = true;
        $config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $this->localeEntityManger = EntityManager::create($dbParamsLocale, $config);
        $this->userEntityManger = EntityManager::create($dbParamsUser, $config);
    }

    public function getUserEntityManger()
    {
        return $this->userEntityManger;
    }

    public function getLocaleEntityManger()
    {
        return $this->localeEntityManger;
    }
}
