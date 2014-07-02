<?php
class Application_Service_Infrastructure_CdnBase
{
    public function __construct()
    {
        require_once 'Zend/Service/Amazon/S3.php';
    }
}