<?php
class Application_Service_Ipaddress
{
    public function __construct()
    {
        self::checkIpaddress();
    }
    protected static function checkIpaddress()
    {
        $ipAdress = $_SERVER['REMOTE_ADDR'];
        $username = "root";
        $password = "password";
        $hostname = "localhost";
        $dbhandle = mysql_connect($hostname, $username, $password);
        $databaseConnection = mysql_select_db("kortingscode_user", $dbhandle);
        $result = mysql_query("SELECT ipaddress FROM ip_addresses where ipaddress ='$ipAdress'");
        $record = mysql_fetch_array($result);
        if (empty($record)) {
            header('Location: http://www.flipit.com/');
        }
    }
}
new Application_Service_Ipaddress();
