<?php

namespace Core\Service;

class Config
{
    private $env;

    public function __construct()
    {
        if (defined('APPLICATION_ENV')) {
            $this->env = defined('APPLICATION_ENV');
        } elseif (getenv('APPLICATION_ENV')) {
            $this->env = getenv('APPLICATION_ENV');
        } else {
            $this->env = 'production';
        }
    }

    public function getConfig()
    {
        return new \Zend_Config_Ini(__DIR__ . '/../../web/application/configs/application.ini', $this->env);
    }

    public function getEnvironment()
    {
        return $this->env;
    }
}
