<?php
class Application_Service_Ipaddress
{
    public function __construct()
    {
        self::checkIpaddress();
    }
    
    protected static function checkIpaddress()
    {
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $dbConnections = $application->getOption('doctrine');
        $dbConnection = $dbConnections['imbull'];
        $splitDbConnectionString = explode('@', $dbConnection);
        $splitDbUsernameAndPassword = explode(':', $splitDbConnectionString[0]);
        $splitDbHostAndDatabaseName = explode('/', $splitDbConnectionString[1]);
        $ipAdress = self::getRealIpAddress();
        $username = ltrim($splitDbUsernameAndPassword[1], '//');
        $password = $splitDbUsernameAndPassword[2];
        $hostname = $splitDbHostAndDatabaseName[0];
        $dbName = $splitDbHostAndDatabaseName[1];
        $dbhandle = mysql_connect($hostname, $username, $password);
        $databaseConnection = mysql_select_db($dbName, $dbhandle) or die('Connection not found');
        $result = mysql_query("SELECT ipaddress FROM ip_addresses where ipaddress ='$ipAdress'");
        $record = mysql_fetch_array($result);
        if (empty($record)) {
            header('Location: /');
            exit();
        }
    }

    protected static function getRealIpAddress()
    {
        $clientIp = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
        $httpXForwardedFor = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        if (!empty($clientIp)) {
            $clientIp = $clientIp;
        } else if (!empty($httpXForwardedFor)) {
            $ipRange = $httpXForwardedFor;
            $clientIp = current(array_slice(explode(",", $ipRange), 0, 1));
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }
        return $clientIp;
    }
}
new Application_Service_Ipaddress();
