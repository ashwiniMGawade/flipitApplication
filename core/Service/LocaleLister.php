<?php

namespace Core\Service;

class LocaleLister
{
    private $env;

    public function __construct()
    {
        $this->env = defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production';
    }

    public function getAllLocales()
    {
        $config = new \Zend_Config_Ini(__DIR__ . '/../../web/application/configs/application.ini', $this->env);
        $locales = array();
        foreach ($config->doctrine as $locale => $dsn) {
            if (is_object($dsn)) {
                $locales[] = $locale;
            }
        }
        return $locales;
    }
}
