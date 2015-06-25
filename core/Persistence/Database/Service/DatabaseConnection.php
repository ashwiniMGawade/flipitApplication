<?php
namespace Core\Persistence\Database\Service;
class DatabaseConnection
{
    public static function getDsn($key = 'en')
    {
        $ini_array = parse_ini_file("..\..\..\..\web\application\configs\application.ini");
        $dsnKeyValue = 'doctrine.'.$key.'.dsn';
        return $ini_array[$dsnKeyValue];
    }
}
