<?php

namespace Command\LocaleMigrations\Helpers;

use \Doctrine\DBAL\Migrations\Configuration\Configuration;
use \Core\Persistence\Database\Service as Service;
use \Doctrine\DBAL\Migrations\OutputWriter;
use \Symfony\Component\Console\Output\OutputInterface;

class ConfigurationHelper
{
    protected $locale;
    protected $configuration;
    protected $connection;
    protected $output;
    protected $outputWriter;

    public function __construct(OutputInterface $output, $locale = null)
    {
        $this->locale = $locale;
        $this->output = $output;
    }

    public function buildLocaleConfiguration()
    {
        self::setConnection('getLocaleEntityManager', $this->locale);

        $configuration = new Configuration($this->connection, $this->getOutputWriter($this->output));
        $configuration->setName('Migrating ' . strtoupper($this->locale) . ' locale');
        $configuration->setMigrationsNamespace('LocaleMigrations');
        $configuration->setMigrationsDirectory(__DIR__ . '/../../../../core/Persistence/Database/Migrations/LocaleMigrations');
        $configuration->setMigrationsTableName('doctrine_migration_versions');
        $configuration->registerMigrationsFromDirectory($configuration->getMigrationsDirectory());

        self::setConfiguration($configuration);
    }

    public function buildUserConfiguration()
    {
        self::setConnection('getUserEntityManager');

        $configuration = new Configuration($this->connection, $this->getOutputWriter($this->output));
        $configuration->setName('Migrating User DB');
        $configuration->setMigrationsNamespace('UserMigrations');
        $configuration->setMigrationsDirectory(__DIR__ . '/../../../../core/Persistence/Database/Migrations/UserMigrations');
        $configuration->setMigrationsTableName('doctrine_migration_versions');
        $configuration->registerMigrationsFromDirectory($configuration->getMigrationsDirectory());

        self::setConfiguration($configuration);
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function setConnection($entityManager, $locale = null)
    {
        $em = (new Service\DoctrineManager(new Service\AppConfig($locale)))->$entityManager();
        $db = $em->getConnection();
        $db->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->connection = $db;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    private function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    private function getOutputWriter(OutputInterface $output)
    {
        if (!$this->outputWriter) {
            $this->outputWriter = new OutputWriter(function ($message) use ($output) {
                return $output->writeln($message);
            });
        }

        return $this->outputWriter;
    }
}
