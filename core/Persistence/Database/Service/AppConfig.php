<?php
namespace Core\Persistence\Database\Service;

class AppConfig implements AppConfigInterface
{
    private $env = '';
    private $locale = '';
    public function __construct($locale = '')
    {
        $this->env = defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production';
        $this->locale = defined('LOCALE') ? LOCALE : '';
        $this->locale = $locale ?: '';
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
                    'password' => 'root'
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => $dbName,
                    'user'     => 'root',
                    'password' => 'root'
                )
            )
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
                    'password' => 'root'
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => 'localhost',
                    'dbname'   => 'flipit_test',
                    'user'     => 'root',
                    'password' => 'root'
                )
            )
        );
    }

    public function getProductionConfig($dbName)
    {
        $config = new \Zend_Config_Ini('../../web/application/configs/application.ini', $this->env);

        $applicationDsn = $config->doctrine->en->dsn;
        $memcacheDsn = $config->resources->frontController->params->memcache;
        $splitDbName = explode('/', $applicationDsn);
        $splitDbUserName = explode(':', $splitDbName[2]);
        $splitDbPassword = explode('@', $splitDbUserName[1]);
        $splitHostName = explode('@', $splitDbUserName[1]);
        $dbPassword = $splitDbPassword[0];
        $dbUserName = $splitDbUserName[0];
        $dbName = $splitDbName[3];
        $hostName = isset($splitHostName[1]) ? $splitHostName[1] : 'localhost';
        $dsn =  array(
            'host'     => $hostName,
            'driver'   => 'pdo_mysql',
            'user'     => $dbUserName,
            'password' => $dbPassword,
            'dbname'   => $dbName
        );

        return array(
            'connections' => array(
                'isDevMode' => false,
                'cacheParams' => $memcacheDsn,
                'proxy_path' => null,
                'path' => array('/../../../../../core/Domain/Entity', '/../../../../../core/Domain/Entity/User'),
                'appMode'=> $this->env,
                'user' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => $dsn['host'],
                    'dbname'   => 'kortingscode_user',
                    'user'     => $dsn['user'],
                    'password' => $dsn['password']
                ),
                'site' => array(
                    'driver'   => 'pdo_mysql',
                    'host'     => $dsn['host'],
                    'dbname'   => $dbName,
                    'user'     => $dsn['user'],
                    'password' => $dsn['password']
                )
            )
        );
    }
}
