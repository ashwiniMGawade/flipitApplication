<?php
namespace Core\Persistence\Adapter;
use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;
use \Doctrine\ORM\Tools\SchemaTool;
class DoctrineLoad
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

     /**
     * @var string
     */
    protected $entityClass = "Core\Domain\Entity\Articles";

    public function __construct($localeDsn)
    {
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../../web/application'));
        require_once APPLICATION_PATH.'/../../vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php';
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH . '/../../vendor/doctrine/common/lib');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', APPLICATION_PATH . '/../../vendor/doctrine/common/lib/Doctrine');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('KC', APPLICATION_PATH . '/../../vendor/doctrine/common/lib');
        $classLoader->register();
        $paths = array(APPLICATION_PATH . '/../../core/Domain/Entity');
        $isDevMode = true;
        $config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $connectionParamsLocale = array(
            'driver'      => 'pdo_mysql',
            'user'        => 'root',
            'password' => 'root',
            'dbname'   => 'flipit_in',
            'host'  => "localhost",
        );
        $emLocale = EntityManager::create($connectionParamsLocale, $config);
        $this->entityManager = $emLocale;
    }

    public function getEnity()
    {
        return $this->entityManager->find($this->entityClass, 1);
    }
}
