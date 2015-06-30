<?php
namespace Core\Persistence\Database\Service;
class DatabaseConnection
{
    public static function getDsn($key = 'en')
    {
        $ini_array = parse_ini_file(__DIR__."/../../../../web/application/configs/application.ini");
        $dsnKeyValue = 'doctrine.'.$key.'.dsn';
        if ($key == 'imbull') {
            $dsnKeyValue = 'doctrine.'.$key;
        }
        return $ini_array[$dsnKeyValue];
    }
}
