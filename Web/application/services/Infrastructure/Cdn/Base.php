<?php
class Application_Service_Infrastructure_Cdn_Base
{
    public function __construct()
    {
        require_once 'Zend/Service/Amazon/S3.php';
    }
}