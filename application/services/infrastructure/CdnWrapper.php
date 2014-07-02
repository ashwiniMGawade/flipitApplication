<?php
class Application_Service_Infrastructure_CdnWrapper
{
    public function putFile($localFilePath, $cdnFilePath)
    {
        require_once 'Zend/Service/Amazon/S3.php';

        $s3 = new Zend_Service_Amazon_S3(S3KEY, S3SECRET);
        $s3->putFile($localFilePath, S3BUCKET.$cdnFilePath);
    }
}