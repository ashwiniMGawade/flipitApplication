<?php
namespace Core\Persistence\Database\Service;

class AppConfig
{
    private $env = '';
    private $locale = '';
    public function __construct()
    {
        $this->env = APPLICATION_ENV ? : 'production';
    }

    public function getConfigs()
    {
        $this->locale  =  $this->locale != '' ? $this->locale : 'en';
        $dbName = $this->locale == 'en' ? 'kortingscode_site' : 'flipit_'.$this->locale;

        if ($this->env === 'development') {
            return $this->getDevelopmentConfig($dbName);
        } elseif ($this->env === 'testing') {
            return $this->getTestingConfig();
        } else {
            return $this->getProductionConfig($dbName);
        }
    }

    public function getDevelopmentConfig($dbName)
    {
        return array(
            'connections' => array(
                'isDevMode' => true,
                'cacheParams' => 'localhost:11211',
                'cache'=> null,
                'proxy_path' => null,
                'path' => array('/../../../../../core/Domain/Entity', '/../../../../../core/Domain/Entity/User'),
                'appMode'=> $this->env,
                'user' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => 'kortingscode_user',
                    'user'     => 'root',
                    'password' => 'root',
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => $dbName,
                    'user'     => 'root',
                    'password' => 'root',
                ),
            ),
        );
    }

    public function getTestingConfig()
    {
        return array(
            'connections' => array(
                'isDevMode' => true,
                'cacheParams' => 'localhost:11211',
                'cache'=> null,
                'proxy_path' => null,
                'path' => array('/../../../../../core/Domain/Entity', '/../../../../../core/Domain/Entity/User'),
                'appMode'=> $this->env,
                'user' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => 'flipit_test_user',
                    'user'     => 'root',
                    'password' => 'root',
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => 'flipit_test',
                    'user'     => 'root',
                    'password' => 'root',
                ),
            ),
        );
    }

    public function getProductionConfig($dbName)
    {
        return array(
            'connections' => array(
                'isDevMode' => false,
                'cacheParams' => 'localhost:11211',
                'proxy_path' => null,
                'path' => array('/../../../../../core/Domain/Entity', '/../../../../../core/Domain/Entity/User'),
                'appMode'=> $this->env,
                'user' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => 'kortingscode_user',
                    'user'     => 'root',
                    'password' => 'root',
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => $dbName,
                    'user'     => 'root',
                    'password' => 'root',
                ),
            ),
        );
    }
}
