<?php
namespace Core\Domain\Factory;

use Core\Persistence\Database\Repository\ApiKeyRepository;
use Core\Persistence\Adapter\DoctrineLoad;

class FactoryRepository
{
    public static function getDsn($key = 'en')
    {
        $ini_array = parse_ini_file("./../../web/application/configs/application.ini");
        $dsnKeyValue = 'doctrine.'.$key.'.dsn';
        if ($key == 'imbull') {
            $dsnKeyValue = 'doctrine.'.$key;
        }
        $doctrineOptions = $ini_array[$dsnKeyValue];
        $splitDbName = explode('/', $doctrineOptions);
        $splitDbUserName = explode(':', $splitDbName[2]);
        $splitDbPassword = explode('@', $splitDbUserName[1]);
        $splitHostName = explode('@', $splitDbUserName[1]);
        $dbPassword = $splitDbPassword[0];
        $dbUserName = $splitDbUserName[0];
        $dbName = $splitDbName[3];
        $hostName = isset($splitHostName[1]) ? $splitHostName[1] : 'localhost';
        return array(
            'host'     => $hostName,
            'driver'   => 'pdo_mysql',
            'user'     => $dbUserName,
            'password' => $dbPassword,
            'dbname'   => $dbName,
        );
    }

    public static function getApiKeys()
    {
        $connectionLocale = self::getDsn();
        $connectionUser = self::getDsn('imbull');

        $DoctrineLoad = new DoctrineLoad($connectionLocale, $connectionUser);
        return new ApiKeyRepository($DoctrineLoad->getUserEntityManger());
    }
}
