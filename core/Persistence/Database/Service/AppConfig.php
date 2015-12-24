<?php
namespace Core\Persistence\Database\Service;

use Core\Service\Config;

class AppConfig
{
    private $env = '';
    private $locale = '';
    private $config = '';
    public function __construct($locale = '')
    {
        $this->env = (new Config)->getEnvironment();
        $this->locale = !empty($locale) ? $locale : (defined('LOCALE') && LOCALE != '' ? LOCALE : 'en');
        $this->config = (new Config)->getConfig();
    }

    public function getConfigs()
    {
        $dbName = $this->locale == 'en' ? 'kortingscode_site' : 'flipit_'.$this->locale;

        if ($this->env === 'development') {
            return $this->getDevelopmentConfig($dbName);
        } elseif ($this->env === 'testing') {
            return $this->getTestingConfig();
        } else {
            return $this->getProductionConfig();
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
                ),
                'dynamoDb' => array(
                    'dynamoDbRegion' => $this->config->dynamodb->dynamodbregion,
                    'accessKey' => $this->config->dynamodb->key,
                    'securityKey' => $this->config->dynamodb->secret
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
                ),
                'dynamoDb' => array(
                    'dynamoDbRegion' => '',
                    'accessKey' => '',
                    'securityKey' => ''
                )
            )
        );
    }

    public function getProductionConfig()
    {
        $locale = $this->locale;
        $applicationDsn = $this->config->doctrine->$locale->dsn;
        $memcacheDsn = $this->config->resources->frontController->params->memcache;
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
                ),
                'dynamoDb' => array(
                    'dynamoDbRegion' => $this->config->dynamodb->dynamodbregion,
                    'accessKey' => $this->config->dynamodb->key,
                    'securityKey' => $this->config->dynamodb->secret
                )
            )
        );
    }
}
