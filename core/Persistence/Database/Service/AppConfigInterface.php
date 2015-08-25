<?php

namespace Core\Persistence\Database\Service;

interface AppConfigInterface
{
    public function getConfigs();

    public function getDevelopmentConfig($dbName);

    public function getTestingConfig();

    public function getProductionConfig($dbName);
}
